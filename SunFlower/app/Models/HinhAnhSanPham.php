<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhAnhSanPham extends Model
{
    use HasFactory;

    protected $table = 'hinhanh_sanpham';

    protected $fillable = ['masp', 'duong_dan', 'thu_tu'];

    /**
     * Ảnh phụ này THUỘC VỀ 1 Sản phẩm.
     */
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'masp', 'masp');
    }
}
