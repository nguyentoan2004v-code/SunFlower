<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function history()
    {
        // Lấy thông tin khách hàng đang đăng nhập
        $khachhang = Auth::guard('khachhang')->user();

        // Lấy lịch sử đơn hàng kèm theo chi tiết sản phẩm (sắp xếp mới nhất lên đầu)
        $donHangs = DonHang::with('sanphams')
            ->where('makh', $khachhang->makh)
            ->orderBy('ngaydat', 'desc')
            ->get();

        return view('auth.order_history', compact('donHangs'));
    }
   // Hiển thị chi tiết đơn hàng
    public function show($madon)
    {
        $donHang = DonHang::with('sanphams')
            ->where('madon', $madon)
            ->firstOrFail();

        // 1. ƯU TIÊN KIỂM TRA THẺ BÀI SESSION (Khách vãng lai vừa đặt đơn)
        $viewedOrders = session()->get('viewed_orders', []);
        if (in_array($madon, $viewedOrders)) {
            // Nếu có thẻ bài -> Cho phép xem thẳng luôn, không cần hỏi nhiều!
            return view('auth.order_detail', compact('donHang'));
        }

        // 2. NẾU KHÔNG CÓ THẺ BÀI -> Bắt buộc kiểm tra đăng nhập (Bảo vệ đơn hàng cũ)
        if ($donHang->makh !== null) {
            if (!Auth::guard('khachhang')->check() || Auth::guard('khachhang')->user()->makh !== $donHang->makh) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền xem đơn hàng này!');
            }
        }

        return view('auth.order_detail', compact('donHang'));
    }

    // Xử lý khách hàng tự hủy đơn
    public function cancel($madon)
    {
        $donHang = DonHang::where('madon', $madon)->firstOrFail();

        // 1. BẢO MẬT: Kiểm tra quyền hủy
        if ($donHang->makh !== null) {
            // Đơn của thành viên -> Phải đăng nhập đúng
            if (!Auth::guard('khachhang')->check() || Auth::guard('khachhang')->user()->makh !== $donHang->makh) {
                return redirect()->route('home')->with('error', 'Bạn không có quyền thao tác trên đơn hàng này!');
            }
        }

        // 2. Tiến hành hủy
        if ($donHang->trangthai == 'Chờ xác nhận') {
            $donHang->update(['trangthai' => 'Đã hủy']);
            
            // Nếu là thành viên thì đẩy về Lịch sử mua hàng, khách vãng lai thì ở lại trang chi tiết
            if (Auth::guard('khachhang')->check()) {
                return redirect()->route('orders.history')->with('success', 'Đã hủy đơn hàng thành công.');
            }
            return back()->with('success', 'Đã hủy đơn hàng thành công.');
        }

        return back()->with('error', 'Đơn hàng đã được xử lý, không thể hủy.');
    }
}