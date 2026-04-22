<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    use HasFactory;

    // 1. Quy tắc 4 dòng cho khóa chính
    protected $table = 'danhmuc';
    protected $primaryKey = 'madm';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. Khai báo các cột được phép thêm dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = ['madm', 'tendm','hinhanh'];

    // 3. THIẾT LẬP MỐI QUAN HỆ (1 Danh mục có NHIỀU Sản phẩm)
    public function sanphams()
    {
        // hasMany(Tên_Model_Con, 'Tên_khóa_ngoại', 'Tên_khóa_chính')
        return $this->hasMany(SanPham::class, 'madm', 'madm');
    }
}