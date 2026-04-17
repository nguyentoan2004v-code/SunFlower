<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class RegisterKhachHangRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Đổi thành true để cho phép mọi người đều có quyền gọi API Đăng ký
        return true; 
    }

    public function rules(): array
    {
        // Bê nguyên cục validation từ Controller sang đây
        return [
            'hoten' => 'required|string|max:40',
            'email' => 'required|string|email|unique:khachhang,email',
            'sdt' => 'required|string|max:15|unique:khachhang,sdt',
            'password' => 'required|string|min:6|confirmed'
        ];
    }

    // BONUS: Bạn có thể tự custom câu báo lỗi cho thân thiện với User Việt Nam
    public function messages(): array
    {
        return [
            'email.unique' => 'Email này đã được sử dụng trong hệ thống.',
            'sdt.unique' => 'Số điện thoại này đã được đăng ký.',
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp.'
        ];
    }
}