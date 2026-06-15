<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\HoaDon;
use App\Models\HangThanhVien;
use App\Models\LichSuDiem;
use App\Models\ChiTietHoaDon;
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
    public function index()
    {
        // Lấy danh sách đơn hàng, mới nhất lên trước
        $orders = DonHang::orderBy('ngaydat', 'desc')->paginate(8);
        return view('admin.orders.index', compact('orders'));
    }

    // 2. Xem chi tiết đơn hàng
    public function show($madon)
    {
        // Lấy đơn hàng kèm chi tiết và thông tin khách hàng
        $order = DonHang::with(['sanphams', 'khachhang'])->findOrFail($madon);
        
        // Kiểm tra xem đơn này đã có hóa đơn chưa (Sửa thành madon)
        $hoadon = HoaDon::where('madon', $madon)->first();

        return view('admin.orders.show', compact('order', 'hoadon'));
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
                    $mahd = 'HD' . date('ymd') . rand(10, 99);
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
            $mahd = 'HD' . date('ymd') . rand(10, 99);

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
   
}