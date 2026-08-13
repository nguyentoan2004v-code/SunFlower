<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhieuHuyHang;
use App\Models\ChiTietPhieuHuy;
use App\Models\LoNguyenLieu;
use App\Models\NguyenLieu;
use App\Models\LichSuKho;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PhieuHuyHangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();

                if (!$user->hasRole('Quản lý Cửa hàng') && !$user->hasRole('Quản lý Kho')) {
                    abort(403, 'Bạn không có quyền thao tác với Phiếu Hủy Hàng!');
                }

                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $query = PhieuHuyHang::with(['nguoiLap', 'nguoiDuyet'])->orderBy('created_at', 'desc');

        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        $phieuHuys = $query->paginate(20);

        return view('admin.phieuhuyhang.index', compact('phieuHuys'));
    }

    public function create(Request $request)
    {
        // Lấy danh sách lô nguyên liệu còn hàng để chọn
        $loNguyenLieus = LoNguyenLieu::with('nguyenLieu')
                                     ->where('soluong_hientai', '>', 0)
                                     ->orderBy('created_at', 'asc')
                                     ->get();

        // Nếu được gọi từ trang Lô nguyên liệu (hủy đích danh), điền trước lô đó
        $preselectedLotId = $request->query('lot_id');

        return view('admin.phieuhuyhang.create', compact('loNguyenLieus', 'preselectedLotId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ghi_chu_chung' => 'nullable|string|max:500',
            'details' => 'required|array|min:1',
            'details.*.id_lo_nguyen_lieu' => 'required|exists:lo_nguyen_lieu,id',
            'details.*.so_luong_huy' => 'required|integer|min:1',
            'details.*.ly_do_chi_tiet' => 'required|string|max:255',
            // image processing if any
        ], [
            'details.required' => 'Vui lòng chọn ít nhất một lô nguyên liệu để hủy.',
            'details.*.so_luong_huy.min' => 'Số lượng hủy phải lớn hơn 0.',
        ]);

        DB::beginTransaction();
        try {
            // Tạo mã phiếu tự động: PH-YYYYMMDD-XXXX
            $datePrefix = Carbon::now()->format('Ymd');
            $lastPhieu = PhieuHuyHang::where('ma_phieu_huy', 'like', "PH-{$datePrefix}-%")->orderBy('id', 'desc')->first();
            $nextId = 1;
            if ($lastPhieu) {
                $lastCode = $lastPhieu->ma_phieu_huy;
                $lastId = intval(substr($lastCode, -4));
                $nextId = $lastId + 1;
            }
            $maPhieuHuy = sprintf("PH-%s-%04d", $datePrefix, $nextId);

            $phieuHuy = PhieuHuyHang::create([
                'ma_phieu_huy' => $maPhieuHuy,
                'manv_lap' => Auth::guard('nhanvien')->user()->manv,
                'ghi_chu_chung' => $request->ghi_chu_chung,
                'trang_thai' => 'Chờ duyệt',
            ]);

            foreach ($request->details as $detail) {
                $lo = LoNguyenLieu::lockForUpdate()->findOrFail($detail['id_lo_nguyen_lieu']);
                $nguyenlieu = NguyenLieu::lockForUpdate()->findOrFail($lo->id_nguyen_lieu);
                $soLuongHuy = $detail['so_luong_huy'];

                if ($soLuongHuy > $lo->soluong_hientai) {
                    throw new \Exception("Số lượng hủy ({$soLuongHuy}) vượt quá tồn kho của Lô {$lo->malo} ({$lo->soluong_hientai}).");
                }

                // Trừ ngay lập tức để giữ hàng (reserve) cho việc hủy
                $lo->soluong_hientai -= $soLuongHuy;
                if ($lo->soluong_hientai == 0) {
                    $lo->trangthai = 'Hết hàng';
                }
                $lo->save();

                $nguyenlieu->tonkho_thucte -= $soLuongHuy;
                $nguyenlieu->save();

                // Lưu chi tiết phiếu
                ChiTietPhieuHuy::create([
                    'id_phieu_huy' => $phieuHuy->id,
                    'id_lo_nguyen_lieu' => $lo->id,
                    'so_luong_huy' => $soLuongHuy,
                    'ly_do_chi_tiet' => $detail['ly_do_chi_tiet'],
                ]);

                // Ghi log trạng thái chờ duyệt
                LichSuKho::create([
                    'id_nguyen_lieu' => $nguyenlieu->id,
                    'loai_gd' => 'pending_waste',
                    'soluong' => -$soLuongHuy,
                    'ghichu' => "Đang chờ duyệt hủy [Phiếu {$phieuHuy->ma_phieu_huy}] - Lô [{$lo->malo}]",
                    'manv' => Auth::guard('nhanvien')->user()->manv,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.phieuhuyhang.index')->with('success', 'Đã lập phiếu hủy hàng thành công và đang chờ duyệt.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $phieuHuy = PhieuHuyHang::with(['nguoiLap', 'nguoiDuyet', 'chiTiet.loNguyenLieu.nguyenLieu'])->findOrFail($id);
        return view('admin.phieuhuyhang.show', compact('phieuHuy'));
    }

    public function approve($id)
    {
        $phieuHuy = PhieuHuyHang::with('chiTiet.loNguyenLieu')->findOrFail($id);

        if ($phieuHuy->trang_thai !== 'Chờ duyệt') {
            return back()->with('error', 'Chỉ có thể duyệt phiếu ở trạng thái chờ.');
        }

        DB::beginTransaction();
        try {
            $phieuHuy->trang_thai = 'Đã duyệt';
            $phieuHuy->manv_duyet = Auth::guard('nhanvien')->user()->manv;
            $phieuHuy->save();

            // Ghi log chính thức
            foreach ($phieuHuy->chiTiet as $chiTiet) {
                LichSuKho::create([
                    'id_nguyen_lieu' => $chiTiet->loNguyenLieu->id_nguyen_lieu,
                    'loai_gd' => 'waste',
                    'soluong' => 0, // Không trừ thêm số lượng vì đã trừ ở bước lập phiếu
                    'ghichu' => "Đã duyệt hủy [Phiếu {$phieuHuy->ma_phieu_huy}] - Lô [{$chiTiet->loNguyenLieu->malo}] (-{$chiTiet->so_luong_huy})",
                    'manv' => Auth::guard('nhanvien')->user()->manv,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Đã duyệt phiếu hủy thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi duyệt phiếu: ' . $e->getMessage());
        }
    }

    public function reject($id)
    {
        $phieuHuy = PhieuHuyHang::with('chiTiet.loNguyenLieu.nguyenLieu')->findOrFail($id);

        if ($phieuHuy->trang_thai !== 'Chờ duyệt') {
            return back()->with('error', 'Chỉ có thể từ chối phiếu ở trạng thái chờ.');
        }

        DB::beginTransaction();
        try {
            $phieuHuy->trang_thai = 'Từ chối';
            $phieuHuy->manv_duyet = Auth::guard('nhanvien')->user()->manv;
            $phieuHuy->save();

            // Hoàn lại số lượng đã trừ
            foreach ($phieuHuy->chiTiet as $chiTiet) {
                $lo = LoNguyenLieu::lockForUpdate()->findOrFail($chiTiet->id_lo_nguyen_lieu);
                $nguyenlieu = NguyenLieu::lockForUpdate()->findOrFail($lo->id_nguyen_lieu);
                
                $lo->soluong_hientai += $chiTiet->so_luong_huy;
                if ($lo->soluong_hientai > 0) {
                    $lo->trangthai = 'Còn hàng'; // Có thể dựa trên HSD để tính toán trạng thái
                }
                $lo->save();

                $nguyenlieu->tonkho_thucte += $chiTiet->so_luong_huy;
                $nguyenlieu->save();

                LichSuKho::create([
                    'id_nguyen_lieu' => $nguyenlieu->id,
                    'loai_gd' => 'refund_waste',
                    'soluong' => $chiTiet->so_luong_huy,
                    'ghichu' => "Từ chối phiếu hủy, hoàn lại [Phiếu {$phieuHuy->ma_phieu_huy}] - Lô [{$lo->malo}]",
                    'manv' => Auth::guard('nhanvien')->user()->manv,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Đã từ chối phiếu hủy và hoàn lại số lượng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi từ chối phiếu: ' . $e->getMessage());
        }
    }
}
