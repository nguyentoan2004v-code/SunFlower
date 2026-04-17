<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang; 
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\RegisterKhachHangRequest; // File khiên bảo vệ đã được gọi

class KhachHangAuthController extends Controller
{
    // 1. SỬA: Thay Request thành RegisterKhachHangRequest
    public function register(RegisterKhachHangRequest $request) {
        
        $khachhang = KhachHang::create([
            // 2. SỬA: Tăng số ngẫu nhiên lên 8 chữ số để mã dài đủ 10 ký tự (Ví dụ: KH12345678)
            'makh' => 'KH' . rand(10000000, 99999999), 
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
        // Bổ sung nhẹ check dữ liệu đầu vào cho Login để tránh lỗi 500 nếu Frontend gửi rỗng
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $kh = KhachHang::where('email', $request->email)->first();
        
        if (!$kh || !Hash::check($request->password, $kh->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không chính xác'
            ], 401);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công',
            'access_token' => $kh->createToken('kh_token')->plainTextToken,
            'data' => $kh
        ], 200);
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
     * Phương thức: PUT
     */
    public function updateProfile(Request $request)
    {
        // Lấy thông tin khách hàng đang đăng nhập từ Token
        $khachhang = $request->user();

        // Validate: Chỉ kiểm tra những trường khách hàng muốn sửa (dùng 'sometimes')
        $request->validate([
            'hoten' => 'sometimes|string|max:40',
            'sdt' => 'sometimes|string|max:15',
            'diachi' => 'sometimes|string|max:100',
        ]);

        // Cập nhật dữ liệu
        // Hàm array_filter giúp loại bỏ những trường bị rỗng (null) mà khách không gửi lên
        $khachhang->update(array_filter($request->only(['hoten', 'sdt', 'diachi'])));

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin cá nhân thành công!',
            'data' => $khachhang
        ], 200);
    }
}