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
            foreach ($phancongs as $manv => $days) {
                foreach ($days as $date => $maca) {
                    // Xóa lịch cũ của người này trong ngày này (để tránh trùng lặp)
                    DB::table('phancong')
                        ->where('manv', $manv)
                        ->where('ngaylam', $date)
                        ->delete();
                    
                    // Nếu Quản lý có chọn Ca (Không chọn "Nghỉ") thì tiến hành insert mới
                    if (!empty($maca)) {
                        DB::table('phancong')->insert([
                            'manv' => $manv,
                            'maca' => $maca,
                            'ngaylam' => $date,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
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
}