<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;

    protected $table = 'chitiet_donhang';
    
    // Tắt tự tăng vì không dùng id
    public $incrementing = false; 
    
    // Cố tình bỏ qua protected $primaryKey để tránh lỗi khóa kép của Laravel

    protected $fillable = ['madon', 'masp', 'soluong', 'giaban'];

    // Chi tiết này thuộc về Đơn hàng nào?
    public function donhang()
    {
        return $this->belongsTo(DonHang::class, 'madon', 'madon');
    }

    // Chi tiết này là của Sản phẩm nào?
    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'masp', 'masp');
    }
}