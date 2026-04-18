<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhachHang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    /**
     * Hiển thị trang Thông tin cá nhân (Profile)
     */
    public function index()
    {
        // 1. Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Session::has('api_token')) {
            return redirect()->route('login')->withErrors(['error' => 'Vui lòng đăng nhập để xem thông tin tài khoản.']);
        }

        // 2. Lấy thông tin user hiện tại từ Session
        $sessionUser = Session::get('user_info');
        $makh = is_array($sessionUser) ? $sessionUser['makh'] : $sessionUser->makh;
        
        // 3. Truy vấn lại DB để lấy dữ liệu mới nhất
        $user = KhachHang::where('makh', $makh)->first();

        // 4. Nếu không tìm thấy khách hàng (ví dụ tài khoản bị xóa), buộc đăng xuất
        if (!$user) {
            Session::forget(['api_token', 'user_info']);
            return redirect()->route('login')->withErrors(['error' => 'Tài khoản không tồn tại.']);
        }

        // 5. Trả về view nằm trong resources/views/auth/profile.blade.php
        return view('auth.profile', compact('user'));
    }

    /**
     * Xử lý Cập nhật thông tin cơ bản (Tên, Số điện thoại, Địa chỉ)
     */
    public function updateProfile(Request $request)
    {
        // 1. Validate dữ liệu gửi lên
        $request->validate([
            'hoten' => 'required|string|max:255',
            'sdt' => 'required|numeric|digits_between:9,11',
            'diachi' => 'nullable|string|max:255', // Đã thêm địa chỉ
        ], [
            'hoten.required' => 'Vui lòng nhập họ tên.',
            'sdt.required' => 'Vui lòng nhập số điện thoại.',
            'sdt.numeric' => 'Số điện thoại chỉ được chứa chữ số.',
            'sdt.digits_between' => 'Số điện thoại không hợp lệ.',
        ]);

        // 2. Lấy Khách hàng hiện tại
        $sessionUser = Session::get('user_info');
        $makh = is_array($sessionUser) ? $sessionUser['makh'] : $sessionUser->makh;
        $user = KhachHang::where('makh', $makh)->first();

        if ($user) {
            // 3. Cập nhật vào Database
            $user->update([
                'hoten' => $request->hoten,
                'sdt' => $request->sdt,
                'diachi' => $request->diachi,
            ]);

            // 4. Cập nhật lại Session mới nhất để Header hiển thị đúng tên mới
            Session::put('user_info', $user->toArray());

            return back()->with('success', 'Đã lưu thay đổi thông tin cá nhân thành công!');
        }

        return back()->with('error', 'Có lỗi xảy ra, không thể cập nhật.');
    }

    /**
     * Xử lý Đổi Mật Khẩu (Giữ lại dự phòng nếu sau này bạn muốn làm thêm chức năng đổi mật khẩu)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        $sessionUser = Session::get('user_info');
        $makh = is_array($sessionUser) ? $sessionUser['makh'] : $sessionUser->makh;
        $user = KhachHang::where('makh', $makh)->first();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }
}