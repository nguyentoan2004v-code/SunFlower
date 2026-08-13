<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\HoaDon;
use App\Models\HangThanhVien;
use App\Models\LichSuDiem;
use App\Models\ChiTietHoaDon;
use App\Models\ChiTietDonHang;
use App\Models\NguyenLieu;
use App\Models\ChiTietDonHangNguyenLieu;
use App\Models\LichSuKho;
use App\Models\LoNguyenLieu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Str;

class OrderController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = auth()->guard('nhanvien')->user();
                
                if (!$user->hasRole('Quản lý Cửa hàng') && !$user->hasRole('Nhân viên Bán hàng')) {
                    abort(403, 'Bạn không có quyền thao tác với Đơn hàng!');
                }
                
                return $next($request);
            }),
        ];
    }
    // 1. Danh sách đơn hàng
    public function index(Request $request)
    {
        $query = DonHang::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('madon', 'like', "%{$search}%")
                  ->orWhere('hoten_nhan', 'like', "%{$search}%")
                  ->orWhere('sdt_nhan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('trangthai')) {
            $query->where('trangthai', $request->trangthai);
        }

        $orders = $query->orderBy('ngaydat', 'desc')->paginate(8)->withQueryString();
        return view('admin.orders.index', compact('orders'));
    }

    // 2. Xem chi tiết đơn hàng
    public function show($madon)
    {
        // Lấy đơn hàng kèm chi tiết, nguyên liệu và thông tin khách hàng
        $order = DonHang::with(['sanphams', 'khachhang', 'chiTietDonHangs.chiTietDonHangNguyenLieus.nguyenLieu'])->findOrFail($madon);
        
        // Kiểm tra xem đơn này đã có hóa đơn chưa (Sửa thành madon)
        $hoadon = HoaDon::where('madon', $madon)->first();

        // BOM: Lấy danh sách nguyên liệu có sẵn để hỗ trợ điều chỉnh thiết kế
        $allNguyenLieus = NguyenLieu::orderBy('ten_nl')->get();

        return view('admin.orders.show', compact('order', 'hoadon', 'allNguyenLieus'));
    }

    // 3. Cập nhật trạng thái & Tự động tạo Hóa đơn
    public function update(Request $request, $madon)
    {
        // Thêm 'khachhang' vào with() để tối ưu truy vấn
        $order = DonHang::with(['sanphams', 'khachhang'])->findOrFail($madon);
        $oldStatus = $order->trangthai;
        $newStatus = $request->trangthai;

        DB::beginTransaction();
        try {
            $order->update(['trangthai' => $newStatus]);

            // Nếu trạng thái chuyển thành "Đã hoàn thành" và trước đó chưa hoàn thành
            if ($newStatus == 'Đã hoàn thành' && $oldStatus != 'Đã hoàn thành') {
                
                // Kiểm tra xem đã có hóa đơn nào cho đơn hàng này chưa để tránh vi phạm UNIQUE constraint
                $invoiceExists = HoaDon::where('madon', $order->madon)->exists();
                
                if (!$invoiceExists) {
                    // --- 1. LOGIC TẠO HÓA ĐƠN ---
                    // Cột mahd trong DB giới hạn 10 ký tự (char 10)
                    $mahd = 'HD' . date('ym') . strtoupper(\Illuminate\Support\Str::random(4));
                    $muc_thue = round($order->tongtien * 8 / 108);

                    $hoadon = HoaDon::create([
                        'mahd'        => $mahd,
                        'madon'       => $order->madon,
                        'tongtien'    => $order->tongtien,
                        'thue'        => $muc_thue, 
                        'ngayxuat'    => now(),
                        'ptthanhtoan' => 'Tiền mặt' 
                    ]);

                    foreach ($order->sanphams as $sp) {
                        ChiTietHoaDon::create([
                            'mahd'      => $hoadon->mahd,
                            'masp'      => $sp->masp,
                            'tensp'     => $sp->tensp, // Snapshot tên sản phẩm
                            'soluong'   => $sp->pivot->soluong,
                            'dongia'    => $sp->pivot->giaban
                        ]);
                    }
                }

                // --- 2. LOGIC TÍCH ĐIỂM & THĂNG HẠNG (Mới) ---
                if ($order->makh && $order->khachhang) {
                    $khachhang = $order->khachhang;
                    $tien_don_hang = $order->tongtien; 
                    
                    // Tính điểm (100.000đ = 10 điểm <=> 10.000đ = 1 điểm)
                    $diem_cong = (int) floor($tien_don_hang / 10000); 

                    // Cộng dồn vào tài khoản khách
                    $khachhang->tong_chi_tieu += $tien_don_hang;
                    $khachhang->diem_thuong += $diem_cong;

                    // Xét thăng hạng: Tìm Hạng có mốc tiền <= tổng chi tiêu (Lấy mốc cao nhất)
                    $hangMoi = HangThanhVien::where('chi_tieu_toi_thieu', '<=', $khachhang->tong_chi_tieu)
                                            ->orderBy('chi_tieu_toi_thieu', 'desc')
                                            ->first();

                    if ($hangMoi) {
                        $khachhang->hang_thanh_vien_id = $hangMoi->id;
                    }

                    $khachhang->save();

                    // Ghi vào bảng lịch sử điểm nếu có điểm cộng
                    if ($diem_cong > 0) {
                        LichSuDiem::create([
                            'makh'           => $khachhang->makh,
                            'loai_giao_dich' => 'cong_diem',
                            'so_diem'        => $diem_cong,
                            'mo_ta'          => 'Tích điểm từ đơn hàng ' . $order->madon
                        ]);
                    }
                }
            }

            // -----------------------------------------------------------
            // LOGIC TRỪ TỒN KHO & LÔ (Khi chuyển sang các trạng thái Đã xử lý)
            // -----------------------------------------------------------
            $processingStatuses = ['Đã xác nhận', 'Đang giao', 'Đã hoàn thành'];
            
            // Nếu chuyển từ Chờ xác nhận -> Đã xác nhận/Đang giao/Đã hoàn thành (Chỉ trừ 1 lần duy nhất)
            if (in_array($newStatus, $processingStatuses) && $oldStatus == 'Chờ xác nhận') {
                $chiTiets = ChiTietDonHang::where('madon', $order->madon)->get();
                foreach ($chiTiets as $ct) {
                    $oims = ChiTietDonHangNguyenLieu::where('id_chitiet_donhang', $ct->id)->get();
                    foreach ($oims as $oim) {
                        $mat = NguyenLieu::lockForUpdate()->find($oim->id_nguyen_lieu);
                        if ($mat) {
                            // Trừ physical_stock và nhả reserved_stock
                            $mat->tonkho_thucte = max(0, $mat->tonkho_thucte - $oim->soluong_dung);
                            $mat->tonkho_datruoc = max(0, $mat->tonkho_datruoc - $oim->soluong_dung);
                            $mat->save();

                            // Trừ Lô theo FEFO
                            $deductedLots = LoNguyenLieu::deductStock($mat->id, $oim->soluong_dung);
                            $lotNotes = implode(', ', array_map(function($l) {
                                return $l['malo'] . ' (-' . $l['deducted_qty'] . ')';
                            }, $deductedLots));

                            // Ghi log
                            LichSuKho::create([
                                'id_nguyen_lieu' => $mat->id,
                                'loai_gd'  => 'order_complete',
                                'soluong'  => -$oim->soluong_dung,
                                'ghichu'   => 'Xác nhận đơn ' . $order->madon . ' [' . $lotNotes . ']',
                                'manv'     => Auth::guard('nhanvien')->user()->manv ?? null,
                            ]);
                        }
                    }
                }
            }

            // -----------------------------------------------------------
            // LOGIC HỦY ĐƠN HÀNG TRONG ADMIN
            // -----------------------------------------------------------
            if ($newStatus == 'Đã hủy' && $oldStatus != 'Đã hủy') {
                if ($oldStatus == 'Chờ xác nhận') {
                    // Trường hợp 1: Hủy sớm -> Nhả reserved_stock, nguyên liệu chưa bị lấy đi
                    $chiTiets = ChiTietDonHang::where('madon', $order->madon)->get();
                    foreach ($chiTiets as $ct) {
                        $oims = ChiTietDonHangNguyenLieu::where('id_chitiet_donhang', $ct->id)->get();
                        foreach ($oims as $oim) {
                            $mat = NguyenLieu::lockForUpdate()->find($oim->id_nguyen_lieu);
                            if ($mat) {
                                $mat->tonkho_datruoc = max(0, $mat->tonkho_datruoc - $oim->soluong_dung);
                                $mat->save();

                                LichSuKho::create([
                                    'id_nguyen_lieu' => $mat->id,
                                    'loai_gd' => 'order_cancel',
                                    'soluong' => $oim->soluong_dung,
                                    'ghichu'  => 'Hủy đơn sớm ' . $order->madon . ' (Hoàn trả tồn tạm giữ)',
                                    'manv'    => Auth::guard('nhanvien')->user()->manv ?? null,
                                ]);
                            }
                        }
                    }
                } else {
                    // Trường hợp 2: Hủy muộn -> Đã lấy hoa ra cắt cắm, không hoàn trả
                    // Không ghi log vô nghĩa vào id_nguyen_lieu=1, admin cần xử lý thủ công
                }
            }

            DB::commit(); 
            return redirect()->route('admin.orders.show', $madon)->with('success', 'Đã cập nhật trạng thái đơn hàng và tích điểm cho khách!');

        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    public function exportInvoice(Request $request, $madon)
    {
        $order = DonHang::with('sanphams')->findOrFail($madon);

        if (HoaDon::where('madon', $madon)->exists()) {
            return back()->with('error', 'Đơn hàng này đã được xuất hóa đơn!');
        }

        try {
            DB::beginTransaction();

            $muc_thue = round($order->tongtien * 8 / 108);
            $mahd = 'HD' . date('ym') . strtoupper(\Illuminate\Support\Str::random(4));

            $hoadon = HoaDon::create([
                'mahd'        => $mahd,
                'tongtien'    => $order->tongtien,
                'thue'        => $muc_thue,
                'ngayxuat'    => now(),
                'ptthanhtoan' => 'Tiền mặt',
                'madon'       => $order->madon,
            ]);

            foreach ($order->sanphams as $sp) {
                ChiTietHoaDon::create([
                    'mahd'    => $hoadon->mahd,
                    'masp'    => $sp->masp,
                    'tensp'   => $sp->tensp,           
                    'soluong' => $sp->pivot->soluong,
                    'dongia'  => $sp->pivot->giaban,   
                ]);
            }

            DB::commit();
            return back()->with('success', 'Xuất hóa đơn thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi khi xuất hóa đơn: ' . $e->getMessage());
        }
    }
public function printInvoice($mahd)
    {
        // Lấy hóa đơn kèm thông tin đơn hàng, khách hàng và chi tiết hóa đơn
        $hoadon = HoaDon::with([
            'donhang.khachhang', 
            'chitiets' // Không cần with('sanpham') nữa vì bạn đã lưu tensp snapshot rồi
        ])->findOrFail($mahd);
        
        // Trả về view in hóa đơn
        return view('admin.orders.print', compact('hoadon'));
    }
    /**
     * BOM: Điều chỉnh nguyên liệu của 1 dòng chi tiết đơn hàng (thay đổi thiết kế theo yêu cầu khách)
     */
    public function adjustMaterials(Request $request, $madon, $detailId)
    {
        $order = DonHang::findOrFail($madon);
        
        // Chỉ cho phép điều chỉnh khi đơn chưa hoàn thành
        if (in_array($order->trangthai, ['Đã hoàn thành', 'Đã hủy'])) {
            return back()->with('error', 'Đơn hàng đã hoàn thành/hủy, không thể điều chỉnh!');
        }

        $chiTiet = ChiTietDonHang::where('id', $detailId)->where('madon', $madon)->firstOrFail();

        $request->validate([
            'new_id_nguyen_lieus'        => 'required|array|min:1',
            'new_id_nguyen_lieus.*'      => 'required|exists:nguyen_lieu,id',
            'new_material_quantities' => 'required|array|min:1',
            'new_material_quantities.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. Nhả reserved_stock cũ của tất cả nguyên liệu hiện tại
            $oldOims = ChiTietDonHangNguyenLieu::where('id_chitiet_donhang', $chiTiet->id)->get();
            foreach ($oldOims as $oim) {
                NguyenLieu::where('id', $oim->id_nguyen_lieu)
                    ->lockForUpdate()
                    ->decrement('tonkho_datruoc', $oim->soluong_dung);

                LichSuKho::create([
                    'id_nguyen_lieu' => $oim->id_nguyen_lieu,
                    'loai_gd' => 'order_cancel',
                    'soluong' => $oim->soluong_dung,
                    'ghichu'  => 'Điều chỉnh thiết kế: nhả NL cũ - Đơn ' . $madon,
                    'manv'    => Auth::guard('nhanvien')->user()->manv,
                ]);
            }

            // 2. Xóa bản sao cũ
            ChiTietDonHangNguyenLieu::where('id_chitiet_donhang', $chiTiet->id)->delete();

            // 3. Tạo bản sao mới + cộng reserved_stock mới
            $materialIds = $request->input('new_id_nguyen_lieus', []);
            $materialQtys = $request->input('new_material_quantities', []);

            foreach ($materialIds as $index => $matId) {
                $qty = (int)($materialQtys[$index] ?? 0);
                if ($qty <= 0) continue;

                // Kiểm tra tồn kho khả dụng
                $mat = NguyenLieu::lockForUpdate()->findOrFail($matId);
                $khaDung = $mat->tonkho_thucte - $mat->tonkho_datruoc;
                if ($khaDung < $qty) {
                    throw new \Exception('Nguyên liệu "' . $mat->ten_nl . '" không đủ (Cần: ' . $qty . ', Khả dụng: ' . $khaDung . ')');
                }

                ChiTietDonHangNguyenLieu::create([
                    'id_chitiet_donhang' => $chiTiet->id,
                    'id_nguyen_lieu'     => $matId,
                    'soluong_dung'       => $qty,
                ]);

                $mat->increment('tonkho_datruoc', $qty);

                LichSuKho::create([
                    'id_nguyen_lieu' => $matId,
                    'loai_gd' => 'order_reserve',
                    'soluong' => -$qty,
                    'ghichu'  => 'Điều chỉnh thiết kế: giữ NL mới - Đơn ' . $madon,
                    'manv'    => Auth::guard('nhanvien')->user()->manv,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.orders.show', $madon)->with('success', 'Đã điều chỉnh nguyên liệu thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi điều chỉnh: ' . $e->getMessage());
        }
    }
   
}
