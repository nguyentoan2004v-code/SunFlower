<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'voucher';
    protected $primaryKey = 'mavoucher';
    public $incrementing = false; // Khóa chính là chuỗi ký tự tự nhập
    protected $keyType = 'string';

    protected $fillable = [
        'mavoucher', 
        'tenvoucher', 
        'loai_giam', 
        'gia_tri_giam', 
        'giam_max', 
        'don_min', 
        'soluong', 
        'da_sudung', 
        'loai_ap_dung', 
        'hien_thi', 
        'ngay_bd', 
        'ngay_kt', 
        'trangthai'
    ];

    // Quan hệ nhiều-nhiều: 1 voucher có thể áp dụng cho nhiều danh mục
    public function danhmucs()
    {
        return $this->belongsToMany(DanhMuc::class, 'voucher_danhmuc', 'mavoucher', 'madm')
                    ->withTimestamps();
    }

    // Quan hệ 1-nhiều: 1 voucher có thể được áp dụng ở nhiều đơn hàng
    public function donhangs()
    {
        return $this->hasMany(DonHang::class, 'mavoucher', 'mavoucher');
    }
}