<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tìm khách hàng theo email
        $khachhang = KhachHang::where('email', $credentials['email'])->first();

        if ($khachhang && Hash::check($credentials['password'], $khachhang->password)) {
            // Đăng nhập thủ công vào Session
            session(['api_token' => 'web_session']); // Giữ lại để các view cũ không bị lỗi
            session(['user_info' => $khachhang->toArray()]);
            
            return redirect()->route('home')->with('success', 'Chào mừng quay trở lại!');
        }

        return back()->withErrors(['email' => 'Thông tin đăng nhập không chính xác.']);
    }

    public function showRegisterForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'hoten' => 'required|string|max:255',
            'email' => 'required|email|unique:khachhang,email',
            'password' => 'required|min:6|confirmed',
            'sdt' => 'required',
        ]);

        $khachhang = KhachHang::create([
            'makh' => 'KH' . rand(100000, 999999),
            'hoten' => $request->hoten,
            'email' => $request->email,
            'sdt' => $request->sdt,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Đăng ký thành công! Hãy đăng nhập.');
    }

    public function logout() {
        session()->forget(['api_token', 'user_info']);
        return redirect()->route('home');
    }
}