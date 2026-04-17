<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiCaller;

class AuthController extends Controller {
    use ApiCaller;

    // Hiện form đăng nhập
    public function showLoginForm() {
        return view('auth.login');
    }

    // Xử lý đăng nhập khách hàng
  public function login(Request $request) {
    $result = $this->callApi('/api/customer/login', 'POST', $request->all());

    // Xóa dòng dd($result) đi nhé bro
    
    if (isset($result['token'])) {
        // Lưu token vào session
        session(['api_token' => $result['token']]);
        session(['user_info' => $result['data']]);
        
        // Quan trọng: Ép session lưu xuống đĩa ngay lập tức
        session()->save(); 

        return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
    }

    return back()->withErrors(['login_error' => 'Thông tin đăng nhập không chính xác.'])->withInput();
}

    // Hiện form đăng ký
    public function showRegisterForm() {
        return view('auth.register');
    }

    // Xử lý đăng ký
    public function register(Request $request) {
        $result = $this->callApi('/api/customer/register', 'POST', $request->all());

        if (isset($result['status']) && $result['status'] == 'success') {
            return redirect()->route('login')->with('success', 'Đăng ký thành công, mời bro đăng nhập!');
        }

        return back()->withErrors(['msg' => $result['message'] ?? 'Lỗi đăng ký!'])->withInput();
    }

    // Đăng xuất
    public function logout() {
        $this->callApi('/api/customer/logout', 'POST');
        session()->forget(['api_token', 'user_info', 'cart']);
        return redirect()->route('home');
    }
}