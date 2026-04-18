<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KhachHang; 
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\RegisterKhachHangRequest;

class KhachHangAuthController extends Controller
{
    /**
     * API Đăng ký khách hàng
     * Phương thức: POST
     */
    public function register(RegisterKhachHangRequest $request) 
    {
        $khachhang = KhachHang::create([
            'makh' => 'KH' . rand(10000000, 99999999), 
            'hoten' => $request->hoten,
            'email' => $request->email,
            'sdt' => $request->sdt,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký thành công! Bạn có thể bổ sung địa chỉ khi đặt hàng.',
            'data' => $khachhang
        ], 201);
    }

    /**
     * API Đăng nhập
     * Phương thức: POST
     */
    public function login(Request $request) 
    {
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
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy thông tin khách hàng thành công!',
            'data' => $request->user()
        ], 200);
    }

    /**
     * API Cập nhật thông tin cá nhân
     * Phương thức: PUT
     */
    public function updateProfile(Request $request)
    {
        $khachhang = $request->user();

        $request->validate([
            'hoten' => 'sometimes|string|max:40',
            'sdt' => 'sometimes|string|max:15',
            'diachi' => 'sometimes|string|max:100',
        ]);

        $khachhang->update(array_filter($request->only(['hoten', 'sdt', 'diachi'])));

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thông tin cá nhân thành công!',
            'data' => $khachhang
        ], 200);
    }

    /**
     * API Đăng xuất
     * Phương thức: POST
     */
    public function logout(Request $request) 
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete(); 
        }
        
        return response()->json([
            'status' => 'success', 
            'message' => 'Đã đăng xuất'
        ], 200);
    }
}