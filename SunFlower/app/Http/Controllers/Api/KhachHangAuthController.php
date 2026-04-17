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
           
            'makh' => 'KH_' . rand(10000, 99999), 
            'hoten' => $request->hoten,
            'email' => $request->email,
            'sdt' => $request->sdt,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Đăng ký thành công!',
            'data' => $khachhang
        ], 201);
    }

    public function login(Request $request) {
        $kh = KhachHang::where('email', $request->email)->first();
        if (!$kh || !Hash::check($request->password, $kh->password)) {
            return response()->json(['message' => 'Thông tin không chính xác'], 401);
        }
        return response()->json([
            'token' => $kh->createToken('kh_token')->plainTextToken, // Đã fix key token
            'data' => $kh
        ]);
    }

    public function me(Request $request) {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ], 200);
    }

    public function updateProfile(Request $request) {
        $khachhang = $request->user();
        $request->validate([
            'hoten' => 'sometimes|string',
            'sdt' => 'sometimes|string',
            'diachi' => 'sometimes|string',
        ]);
        $khachhang->update(array_filter($request->only(['hoten', 'sdt', 'diachi'])));

        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thành công!',
            'data' => $khachhang
        ], 200);
    }

    public function logout(Request $request) {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete(); 
        }
        return response()->json(['status' => 'success', 'message' => 'Đã đăng xuất'], 200);
    }
}