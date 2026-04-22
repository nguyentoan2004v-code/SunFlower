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
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Kiểm tra xem Email có tồn tại trong CSDL hay không
        $khachhang = KhachHang::where('email', $request->email)->first();

        if (!$khachhang) {
            // Trả về lỗi riêng cho email và GIỮ LẠI email vừa nhập
            return back()
                ->withErrors(['email' => 'Tài khoản Email không tồn tại.'])
                ->withInput($request->only('email'));
        }

        // 2. Nếu email đúng, tiếp tục kiểm tra mật khẩu
        if (!Hash::check($request->password, $khachhang->password)) {
            // Trả về lỗi riêng cho password và GIỮ LẠI email vừa nhập
            return back()
                ->withErrors(['password' => 'Mật khẩu không chính xác.'])
                ->withInput($request->only('email'));
        }



        // 2. Nếu email đúng, tiếp tục kiểm tra mật khẩu
        if (!Hash::check($request->password, $khachhang->password)) {
            // Trả về lỗi riêng cho password và GIỮ LẠI email vừa nhập
            return back()
                ->withErrors(['password' => 'Mật khẩu không chính xác.'])
                ->withInput($request->only('email'));
        }

        // 3. Nếu đúng cả email và mật khẩu -> Tiến hành đăng nhập
        Auth::guard('khachhang')->login($khachhang);
        
        // Tạo lại session để bảo mật
        $request->session()->regenerate();
        

        // Giữ lại session thủ công cũ của bạn
        session(['api_token' => 'web_session']);
        session(['user_info' => $khachhang->toArray()]);
        
        return redirect()->route('home')->with('success', 'Chào mừng quay trở lại!');
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

    public function logout(Request $request) {
        // 1. Đăng xuất khỏi hệ thống Auth của Guard
        Auth::guard('khachhang')->logout();
        
        // 2. Hủy toàn bộ token và phiên làm việc (Bảo mật)
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // 3. Xóa các session thủ công cũ (nếu có)
        session()->forget(['api_token', 'user_info']);
        
        return redirect()->route('home');
    }
}