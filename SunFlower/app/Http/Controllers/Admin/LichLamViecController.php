<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LichLamViec;
use App\Models\NhanVien;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LichLamViecController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                if ($request->route()->getActionMethod() === 'mySchedule') {
                    return $next($request);
                }
                if (!$user->hasRole('Quản lý Cửa hàng')) {
                    abort(403, 'Chỉ Quản lý Cửa hàng mới có quyền xếp lịch làm việc!');
                }
                return $next($request);
            }),
        ];
    }

    // 1. Hiển thị Ma trận thời khóa biểu theo tuần
    public function index(Request $request)
    {
        Carbon::setLocale('vi'); // Hiển thị Thứ bằng tiếng Việt
        
        $caLamViecs = LichLamViec::orderBy('giolam')->get();
        $nhanviens = NhanVien::all();

        // Xác định ngày đầu tuần (Thứ 2) dựa vào tham số trên URL
        $startOfWeek = $request->has('week') 
            ? Carbon::parse($request->week)->startOfWeek() 
            : Carbon::now()->startOfWeek();
        
        $endOfWeek = $startOfWeek->copy()->endOfWeek(); // Chủ nhật

        // Tạo mảng chứa 7 ngày trong tuần
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfWeek->copy()->addDays($i);
        }

        // Lấy toàn bộ phân công của tuần này
        $phancongs = DB::table('phancong')
            ->whereBetween('ngaylam', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
            ->get();

        // Sắp xếp dữ liệu thành một "Ma trận": $matrix['Mã NV']['Ngày'] = 'Mã Ca'
        $matrix = [];
        foreach ($phancongs as $pc) {
            $matrix[$pc->manv][$pc->ngaylam] = $pc->maca;
        }

        return view('admin.lichlamviec.index', compact('caLamViecs', 'nhanviens', 'days', 'startOfWeek', 'matrix'));
    }

    // 2. Xử lý lưu dữ liệu Ma trận
    public function saveWeekly(Request $request)
    {
        // Nhận về một mảng ma trận từ form
        $phancongs = $request->input('phancong', []);
        $weekParam = $request->input('current_week'); // Để redirect về đúng tuần vừa sửa
        
        DB::beginTransaction();
        try {
            $manvList = array_keys($phancongs);
            $dateList = [];
            foreach ($phancongs as $days) {
                foreach (array_keys($days) as $date) {
                    $dateList[] = $date;
                }
            }
            $dateList = array_unique($dateList);
            
            if (!empty($manvList) && !empty($dateList)) {
                // Xóa lịch cũ của những người này trong các ngày đã chọn (Bulk Delete)
                DB::table('phancong')
                    ->whereIn('manv', $manvList)
                    ->whereIn('ngaylam', $dateList)
                    ->delete();
            }

            $inserts = [];
            foreach ($phancongs as $manv => $days) {
                foreach ($days as $date => $maca) {
                    // Nếu Quản lý có chọn Ca (Không chọn "Nghỉ") thì tiến hành chuẩn bị dữ liệu insert mới
                    if (!empty($maca)) {
                        $inserts[] = [
                            'manv' => $manv,
                            'maca' => $maca,
                            'ngaylam' => $date,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            // Chèn tất cả bằng 1 lệnh duy nhất (Bulk Insert)
            if (!empty($inserts)) {
                DB::table('phancong')->insert($inserts);
            }
            DB::commit();
            return redirect()->route('admin.lichlamviec.index', ['week' => $weekParam])
                             ->with('success', 'Đã lưu lịch làm việc tuần thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi lưu: ' . $e->getMessage());
        }
    }

    // 3. Hàm xem lịch cá nhân của nhân viên (Giữ nguyên như lúc nãy)
    public function mySchedule(Request $request)
    {
        $user = auth()->guard('nhanvien')->user();
        \Carbon\Carbon::setLocale('vi');

        $startOfWeek = $request->has('week') 
            ? \Carbon\Carbon::parse($request->week)->startOfWeek() 
            : \Carbon\Carbon::now()->startOfWeek();
        
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        // Lấy lịch làm việc CỦA TUẦN NÀY
        $lichs = $user->lichlamviecs()
            ->wherePivot('ngaylam', '>=', $startOfWeek->format('Y-m-d'))
            ->wherePivot('ngaylam', '<=', $endOfWeek->format('Y-m-d'))
            ->get();

        // Tạo mảng 7 ngày
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = $startOfWeek->copy()->addDays($i);
        }

        return view('admin.lichlamviec.myschedule', compact('days', 'startOfWeek', 'lichs'));
    }

    public function autoGenerate(Request $request)
    {
        $weekParam = $request->input('week');
        $startOfWeek = $weekParam 
            ? Carbon::parse($weekParam)->startOfWeek() 
            : Carbon::now()->startOfWeek();
        
        $endOfWeek = $startOfWeek->copy()->endOfWeek();

        $nhanviens = NhanVien::all();
        
        DB::beginTransaction();
        try {
            // 1. Xóa sạch lịch cũ của tuần này
            DB::table('phancong')
                ->whereBetween('ngaylam', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')])
                ->delete();

            // 2. Tách riêng Sếp (Admin) và Nhân viên thường
            $admins = [];
            $staffs = [];
            foreach ($nhanviens as $nv) {
                if ($nv->hasRole('Quản lý Cửa hàng')) $admins[] = $nv;
                else $staffs[] = $nv;
            }

            // --- LUỒNG 1: ADMIN (Làm HC T2-T7, nghỉ CN) ---
            $adminInserts = [];
            foreach ($admins as $admin) {
                for ($i = 0; $i < 6; $i++) { // 6 ngày đầu tuần
                    $adminInserts[] = [
                        'manv' => $admin->manv,
                        'maca' => 'CA_HC',
                        'ngaylam' => $startOfWeek->copy()->addDays($i)->format('Y-m-d'),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
            if (!empty($adminInserts)) {
                DB::table('phancong')->insert($adminInserts);
            }

            // --- LUỒNG 2: NHÂN VIÊN THƯỜNG (3 Sáng - 3 Tối) ---
            // Dùng thuật toán thử lại (Retry) để tìm ra lịch hoàn hảo, không có ngày nào bị bỏ trống ca
            $bestSchedule = [];
            $maxRetries = 100; // Cho máy xóc tối đa 100 lần để tìm tỷ lệ đẹp nhất
            
            for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
                $tempSchedule = [];
                $dayCoverage = []; // Đếm số lượng ca Sáng/Tối của mỗi ngày
                
                // A. Lên lịch nghỉ: Chia đều ngày nghỉ cho nhân viên
                $availableDays = [0, 1, 2, 3, 4, 5, 6];
                shuffle($availableDays);
                $daysOff = [];
                foreach ($staffs as $index => $staff) {
                    $daysOff[$staff->manv] = $availableDays[$index % 7];
                }

                // B. Tạo kho ca cho mỗi người (Đúng 3 Sáng, 3 Tối)
                $shiftPools = [];
                foreach ($staffs as $staff) {
                    $pool = ['CA_SANG', 'CA_SANG', 'CA_SANG', 'CA_TOI', 'CA_TOI', 'CA_TOI'];
                    shuffle($pool); // Xóc ngẫu nhiên túi ca này
                    $shiftPools[$staff->manv] = $pool;
                }

                // C. Bắt đầu rải ca ra các ngày
                for ($i = 0; $i < 7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
                    $dayCoverage[$date] = [];
                    
                    foreach ($staffs as $staff) {
                        // Nếu hôm nay không phải ngày nghỉ của nhân viên này
                        if ($daysOff[$staff->manv] != $i) {
                            $shift = array_pop($shiftPools[$staff->manv]); // Rút 1 ca từ túi ra
                            $tempSchedule[] = [
                                'manv' => $staff->manv,
                                'maca' => $shift,
                                'ngaylam' => $date,
                                'created_at' => now(),
                                'updated_at' => now()
                            ];
                            $dayCoverage[$date][] = $shift;
                        }
                    }
                }

                // D. KIỂM TRA LỊCH HOÀN HẢO: Ngày nào cũng phải có người trực Sáng và người trực Tối
                $isValid = true;
                foreach ($dayCoverage as $date => $shifts) {
                    // Nếu ngày đó có từ 2 người đi làm trở lên mà lại thiếu Sáng hoặc Tối -> Xếp lại
                    if (count($shifts) >= 2) {
                        if (!in_array('CA_SANG', $shifts) || !in_array('CA_TOI', $shifts)) {
                            $isValid = false;
                            break;
                        }
                    }
                }

                if ($attempt == 0) $bestSchedule = $tempSchedule; // Giữ lại bản nháp đầu tiên phòng hờ
                if ($isValid) {
                    $bestSchedule = $tempSchedule; // Tìm thấy lịch hoàn hảo -> Chốt luôn!
                    break;
                }
            }

            // 3. Insert lịch hoàn hảo vào DB
            if (!empty($bestSchedule)) {
                DB::table('phancong')->insert($bestSchedule);
            }
            
            DB::commit();
            return redirect()->route('admin.lichlamviec.index', ['week' => $weekParam])
                             ->with('success', 'Đã xếp lịch tự động cho tuần thành công!');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra khi tự xếp lịch: ' . $e->getMessage());
        }
    }
}