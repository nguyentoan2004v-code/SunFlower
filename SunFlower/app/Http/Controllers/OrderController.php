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
            ->paginate(10);

        return view('auth.order_history', compact('donHangs'));
    }
   // Hiển thị chi tiết đơn hàng
    public function show(Request $request, $madon)
    {
        $donHang = DonHang::with('sanphams')
            ->where('madon', $madon)
            ->firstOrFail();

        // Ưu tiên kiểm tra Token bảo mật trên URL (Cho khách vãng lai dùng link lưu trữ)
        // Dùng !empty() để loại trừ null/rỗng, hash_equals() để chống timing attack
        $hasToken = !empty($donHang->token)
            && hash_equals((string) $donHang->token, (string) $request->query('token', ''));
        if ($hasToken) {
            return view('auth.order_detail', compact('donHang'));
        }

        // 1. ƯU TIÊN KIỂM TRA THẺ BÀI SESSION (Khách vãng lai vừa đặt đơn)
        $viewedOrders = session()->get('viewed_orders', []);
        if (in_array($madon, $viewedOrders)) {
            // Nếu có thẻ bài -> Cho phép xem thẳng luôn, không cần hỏi nhiều!
            return view('auth.order_detail', compact('donHang'));
        }

        // 2. NẾU KHÔNG CÓ THẺ BÀI VÀ TOKEN -> Bắt buộc kiểm tra đăng nhập (Bảo vệ đơn hàng)
        if (!Auth::guard('khachhang')->check() || Auth::guard('khachhang')->user()->makh !== $donHang->makh) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền xem đơn hàng này!');
        }

        return view('auth.order_detail', compact('donHang'));
    }

    // Xử lý khách hàng tự hủy đơn
    public function cancel(Request $request, $madon)
    {
        $donHang = DonHang::where('madon', $madon)->firstOrFail();

        // BẢO MẬT: Hủy đơn là hành động không thể hoàn tác — chỉ chấp nhận 2 tầng xác thực:
        // - $isOwner : khách hàng đã đăng nhập và là chủ đơn hàng
        // - $hasToken: khách vãng lai có token hợp lệ từ URL (ví dụ: link trong trang xác nhận)
        // Session ($hasSession) bị loại bỏ ở đây vì session có thể bị giữ lại và khai thác.
        // Dùng !empty() để an toàn với token null (đơn cũ chưa có token).
        // Dùng hash_equals() thay vì === để chống timing attack.
        $isOwner  = Auth::guard('khachhang')->check()
            && Auth::guard('khachhang')->user()->makh === $donHang->makh;
        $hasToken = !empty($donHang->token)
            && hash_equals((string) $donHang->token, (string) $request->query('token', ''));

        if (!$isOwner && !$hasToken) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền hủy đơn hàng này!');
        }

        // Tiến hành hủy
        if ($donHang->trangthai == 'Chờ xác nhận') {
            $donHang->update(['trangthai' => 'Đã hủy']);

            // Thành viên → Lịch sử mua hàng | Khách vãng lai → Ở lại trang chi tiết
            if (Auth::guard('khachhang')->check()) {
                return redirect()->route('orders.history')->with('success', 'Đã hủy đơn hàng thành công.');
            }
            return back()->with('success', 'Đã hủy đơn hàng thành công.');
        }

        return back()->with('error', 'Đơn hàng đã được xử lý, không thể hủy.');
    }
}