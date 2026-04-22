<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('admin.auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Sử dụng guard nhanvien để kiểm tra đăng nhập
        if (Auth::guard('nhanvien')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors(['email' => 'Thông tin đăng nhập không chính xác.']);
    }

    public function logout(Request $request)
{
    // Chỉ đăng xuất tài khoản của guard 'nhanvien'
    Auth::guard('nhanvien')->logout();

    // Xóa toàn bộ session hiện tại để bảo mật
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // DÒNG QUAN TRỌNG NHẤT: Bắt buộc chuyển hướng về trang login của admin
    return redirect()->route('admin.login'); 
}
}
