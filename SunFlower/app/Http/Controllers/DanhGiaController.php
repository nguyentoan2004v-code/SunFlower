<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DanhGia;
use App\Models\ChiTietDonHang;
use Illuminate\Support\Facades\Auth;

class DanhGiaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'masp' => 'required|string|exists:sanpham,masp',
            'madon' => 'required|string|exists:donhang,madon',
            'so_sao' => 'required|integer|min:1|max:5',
            'binh_luan' => 'nullable|string|max:1000',
        ]);

        $makh = Auth::guard('khachhang')->id(); // Lấy mã KH từ guard khách hàng

        // Kiểm tra xem khách hàng này có mua sản phẩm này trong đơn hàng này không
        $daMuaHang = ChiTietDonHang::where('madon', $request->madon)
            ->where('masp', $request->masp)
            ->exists();

        if (!$daMuaHang) {
            return back()->with('error', 'Bạn không thể đánh giá sản phẩm chưa mua trong đơn hàng này!');
        }

        // Kiểm tra xem đã đánh giá chưa (mỗi sản phẩm trong 1 đơn hàng chỉ đánh giá 1 lần)
        $daDanhGia = DanhGia::where('madon', $request->madon)
            ->where('masp', $request->masp)
            ->exists();

        if ($daDanhGia) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi!');
        }

        DanhGia::create([
            'makh' => $makh,
            'masp' => $request->masp,
            'madon' => $request->madon,
            'so_sao' => $request->so_sao,
            'binh_luan' => $request->binh_luan,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}