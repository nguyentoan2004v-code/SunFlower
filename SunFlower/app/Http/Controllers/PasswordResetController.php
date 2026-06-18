<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    // =====================================================================
    // BƯỚC 1 — Hiển thị form nhập email
    // =====================================================================

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // =====================================================================
    // BƯỚC 2 — Gửi link reset qua email
    // Route có throttle:5,1 nên không throttle thêm ở đây
    // =====================================================================

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Dùng đúng broker 'khachhangs' — KHÔNG dùng Password::sendResetLink()
        // (broker mặc định là 'users', sẽ lookup nhầm bảng)
        $status = Password::broker('khachhangs')->sendResetLink(
            $request->only('email')
        );

        // Password::RESET_LINK_SENT kể cả khi email không tồn tại → tránh email enumeration
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Nếu email tồn tại trong hệ thống, chúng tôi đã gửi link đặt lại mật khẩu. Vui lòng kiểm tra hộp thư.')
            : back()->withErrors(['email' => 'Có lỗi xảy ra khi gửi email. Vui lòng thử lại sau.']);
    }

    // =====================================================================
    // BƯỚC 3 — Hiển thị form nhập mật khẩu mới
    // =====================================================================

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // =====================================================================
    // BƯỚC 4 — Xử lý đặt lại mật khẩu
    // =====================================================================

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => 'required',
            'email'                 => 'required|email',
            'password'              => 'required|min:6|confirmed',
            // 'confirmed' tự động kiểm tra field 'password_confirmation'
            // → View bắt buộc phải có <input name="password_confirmation">
        ]);

        $status = Password::broker('khachhangs')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Cập nhật mật khẩu mới
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // KHÔNG gọi $user->setRememberToken()
                // Lý do: bảng khachhang không có cột remember_token
                // → Gọi sẽ throw PDOException âm thầm
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', '🎉 Mật khẩu đã được đặt lại thành công! Vui lòng đăng nhập.')
            : back()->withErrors(['email' => __($status)])->withInput($request->only('email'));
    }
}
