<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'hoten_nguoi_nhan' => 'required|string|max:40',
            'sdt_nhan' => 'required|string|max:15',
            'diachi_giao' => 'required|string|max:100',
            'ghichu' => 'nullable|string',
            'tongtien' => 'required|numeric|min:0',
            
            // Validate mảng giỏ hàng cực xịn của Laravel
            'cart' => 'required|array|min:1', // Phải là mảng, có ít nhất 1 món
            'cart.*.masp' => 'required|string|exists:sanpham,masp', // Mã hoa PHẢI TỒN TẠI trong kho
            'cart.*.soluong' => 'required|integer|min:1', // Không được mua âm bó hoa
            'cart.*.dongia' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'cart.*.masp.exists' => 'Có sản phẩm trong giỏ hàng không tồn tại hoặc đã ngừng kinh doanh.',
        ];
    }
}