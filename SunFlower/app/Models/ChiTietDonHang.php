<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;

    protected $table = 'chitiet_donhang';

    // BOM REFACTOR: Dùng cột id auto-increment mới thay cho composite PK cũ
    protected $primaryKey = 'id';
    public $incrementing = true;

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

    /**
     * BOM: Danh sách nguyên liệu (bản sao) gắn với dòng chi tiết đơn hàng này
     */
    public function chiTietDonHangNguyenLieus()
    {
        return $this->hasMany(ChiTietDonHangNguyenLieu::class, 'id_chitiet_donhang');
    }

    /**
     * BOM: Truy cập nhanh danh sách nguyên liệu qua quan hệ N-N
     */
    public function nguyenLieus()
    {
        return $this->belongsToMany(NguyenLieu::class, 'chitiet_donhang_nguyenlieu', 'id_chitiet_donhang', 'id_nguyen_lieu')
                    ->withPivot('soluong_dung')
                    ->withTimestamps();
    }
}