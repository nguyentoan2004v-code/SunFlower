<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DonHang;
use App\Models\HoaDon;
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
        $order = DonHang::with('sanphams')->findOrFail($madon);
        $oldStatus = $order->trangthai;
        $newStatus = $request->trangthai;

        DB::beginTransaction();
        try {
            $order->update(['trangthai' => $newStatus]);

            // 2. Nếu trạng thái chuyển thành "Đã hoàn thành" và chưa có hóa đơn
            if ($newStatus == 'Đã hoàn thành' && $oldStatus != 'Đã hoàn thành') {
                
                // SỬA LỖI 1: Tạo mã HD ngắn gọn 10 ký tự (HD + NămThángNgày + 2 số ngẫu nhiên)
                // Ví dụ hôm nay 22/04/26 -> HD26042299
                $mahd = 'HD' . date('ymd') . rand(10, 99);

                // SỬA LỖI 2: Khớp 100% tên cột theo bảng hoadon của Toàn
                $hoadon = HoaDon::create([
                    'mahd'        => $mahd,
                    'madon'       => $order->madon,
                    'tongtien'    => $order->tongtien,
                    'thue'        => 0, // Giả sử thuế là 0%, bạn có thể đổi thành 8 hoặc 10
                    'ngayxuat'    => now(),
                    'ptthanhtoan' => 'Tiền mặt' // Mặc định là tiền mặt, hoặc lấy từ Đơn hàng nếu có
                ]);

                // Copy từ Chi Tiết Đơn Hàng sang Chi Tiết Hóa Đơn
                foreach ($order->sanphams as $sp) {
                    ChiTietHoaDon::create([
                        'mahd'      => $hoadon->mahd,
                        'masp'      => $sp->masp,
                        'soluong'   => $sp->pivot->soluong,
                        'dongia'    => $sp->pivot->giaban, // Hoặc dongia tùy tên cột của bạn
                        'thanhtien' => $sp->pivot->soluong * $sp->pivot->giaban
                    ]);
                }
            }

            DB::commit(); 
            return redirect()->route('admin.orders.show', $madon)->with('success', 'Đã cập nhật trạng thái đơn hàng!');

        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    public function exportInvoice(Request $request, $madon)
{
    $order = DonHang::findOrFail($madon);

    // Kiểm tra xem đã có hóa đơn chưa (dù database đã bắt unique, nhưng bắt ở Controller để hiện thông báo đẹp hơn)
    if (HoaDon::where('madon', $madon)->exists()) {
        return back()->with('error', 'Đơn hàng này đã được xuất hóa đơn!');
    }

    try {
        DB::beginTransaction();

        // Giả sử thuế là 8% VAT trên tổng tiền (Bạn có thể đổi logic này theo nghiệp vụ)
        $muc_thue = $order->tongtien * 0.08; 
        
        // 1. Tạo hóa đơn mới
        $hoadon = HoaDon::create([
            'mahd' => 'HD' . strtoupper(Str::random(6)), // Sinh mã HD ngẫu nhiên độ dài 10 char
            'tongtien' => $order->tongtien + $muc_thue,
            'thue' => $muc_thue,
            'ngayxuat' => now()->toDateString(), // Định dạng date
            'ptthanhtoan' => 'Tiền mặt', // Hoặc lấy từ form request nếu bạn có chọn phương thức
            'madon' => $order->madon,
        ]);

        // 2. Sao chép chi tiết từ đơn hàng sang chi tiết hóa đơn
        foreach ($order->sanphams as $sp) {
            ChiTietHoaDon::create([
                'mahd'    => $hoadon->mahd,
                'masp'    => $sp->masp,
                'tensp'   => $sp->sanpham->tensp,
                'soluong' => $sp->pivot->soluong,
                'dongia'  => $sp->giaban,
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