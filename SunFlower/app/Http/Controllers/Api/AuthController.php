<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\NhanVien;

class AuthController extends Controller
{
    /**
     * [Giai đoạn 2.1] API Đăng nhập
     * Phương thức: POST
     */
   public function login(Request $request)
    {
        // 1. Validate: Ép phải là chuẩn email (có chữ @)
        $request->validate([
            'email' => 'required|email', 
            'password' => 'required|string'
        ]);

        // 2. Tìm nhân viên bằng CỘT EMAIL
        $nhanvien = NhanVien::where('email', $request->email)->first();

        // 3. Kiểm tra chéo
        if (!$nhanvien || !Hash::check($request->password, $nhanvien->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email hoặc mật khẩu không chính xác!'
            ], 401);
        }

        // 4. Mật khẩu đúng -> Cấp thẻ Token
        $token = $nhanvien->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng nhập thành công!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'nhanvien' => $nhanvien->load('vaitros')
        ], 200);
    }
    /**
     * [Giai đoạn 2.2] API Lấy thông tin cá nhân của người đang đăng nhập
     * Phương thức: GET
     */
    public function me(Request $request)
    {
        // Khi đã vượt qua trạm gác, Laravel tự động biết người cầm thẻ là ai.
        // Bạn chỉ cần gọi $request->user() là lấy được toàn bộ thông tin nhân viên đó.
        
        $nhanvien = $request->user()->load('vaitros'); // Load kèm vai trò để hiển thị ra UI

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy thông tin thành công!',
            'data' => $nhanvien
        ], 200);
    }
}