<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;

    // 1. Quy tắc 4 dòng cho khóa chính
    protected $table = 'sanpham';
    protected $primaryKey = 'masp';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. Khai báo các cột được phép nhập
    protected $fillable = ['masp', 'tensp', 'giaban', 'mota', 'giakm', 'madm'];

    // 3. THIẾT LẬP MỐI QUAN HỆ (1 Sản phẩm THUỘC VỀ 1 Danh mục)
    public function danhmuc()
    {
        // belongsTo(Tên_Model_Cha, 'Tên_khóa_ngoại', 'Tên_khóa_chính_của_cha')
        return $this->belongsTo(DanhMuc::class, 'madm', 'madm');
    }
    // Quan hệ N-N: 1 Sản phẩm nằm trong NHIỀU Đơn hàng
    public function donhangs()
    {
        return $this->belongsToMany(DonHang::class, 'chitietdonhang', 'masp', 'madon')
                    ->withPivot('soluong', 'dongia')
                    ->withTimestamps();
    }
}