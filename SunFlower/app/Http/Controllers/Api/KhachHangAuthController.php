<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\KhachHang; 
use Illuminate\Support\Facades\Hash;

class KhachHangAuthController extends Controller
{
  public function register(Request $request) {
    
    $request->validate([
        'hoten' => 'required|string',
        'email' => 'required|email|unique:khachhang',
        'sdt' => 'required|string',
        'password' => 'required|min:6'
    ]);

    
    $khachhang = KhachHang::create([
        'makh' => 'KH' . rand(100, 999),
        'hoten' => $request->hoten,
        'email' => $request->email,
        'sdt' => $request->sdt,
        'password' => Hash::make($request->password),
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Đăng ký thành công! Bạn có thể bổ sung địa chỉ khi đặt hàng.'
    ], 201);
}

public function login(Request $request) {
    $kh = KhachHang::where('email', $request->email)->first();
    if (!$kh || !Hash::check($request->password, $kh->password)) {
        return response()->json(['message' => 'Thông tin không chính xác'], 401);
    }
    return response()->json([
        'access_token' => $kh->createToken('kh_token')->plainTextToken,
        'data' => $kh
    ]);
}
/**
     * API Lấy thông tin khách hàng đang đăng nhập
     * Phương thức: GET
     */
    public function me(Request $request)
    {
        
        $khachhang = $request->user();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy thông tin khách hàng thành công!',
            'data' => $khachhang
        ], 200);
    }
    /**
     * API Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        // Lấy thông tin khách hàng đang đăng nhập từ Token
        $khachhang = $request->user();

        // Validate: Chỉ kiểm tra những trường khách hàng muốn sửa (dùng 'sometimes')
        $request->validate([
            'hoten' => 'sometimes|string',
            'sdt' => 'sometimes|string',
            'diachi' => 'sometimes|string',
            // Không cho phép đổi Email và Password ở API này để bảo mật
        ]);

        // Cập nhật dữ liệu
        // Hàm array_filter giúp loại bỏ những trường bị rỗng (null) mà khách không gửi lên
        $khachhang->update(array_filter($request->only(['hoten', 'sdt', 'diachi'])));

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin thành công!',
            'data' => $khachhang
        ], 200);
    }
}
