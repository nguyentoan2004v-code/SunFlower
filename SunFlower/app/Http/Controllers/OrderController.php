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
    // Xem chi tiết một đơn hàng cụ thể
    public function show($madon)
    {
        $donHang = DonHang::with('sanphams')
            ->where('madon', $madon)
            ->where('makh', Auth::guard('khachhang')->id()) // Bảo mật: chỉ chủ đơn mới xem được
            ->firstOrFail();

        return view('auth.order_detail', compact('donHang'));
    }

    // Khách hàng tự hủy đơn hàng (nếu đơn vẫn đang ở trạng thái 'Chờ xác nhận')
    public function cancel($madon)
    {
        $donHang = DonHang::where('madon', $madon)
            ->where('makh', Auth::guard('khachhang')->id())
            ->firstOrFail();

        if ($donHang->trangthai == 'Chờ xác nhận') {
            $donHang->update(['trangthai' => 'Đã hủy']);
            return back()->with('success', 'Đã hủy đơn hàng thành công.');
        }

        return back()->with('error', 'Không thể hủy đơn hàng ở trạng thái này.');
    }
}