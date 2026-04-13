<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuGia extends Model
{
    protected $table = 'lich_su_gia';
    protected $primaryKey = 'magia';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['magia', 'masp', 'giaban', 'ngay_ap_dung'];

    // Thuộc về một sản phẩm cụ thể
    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'masp', 'masp');
    }
}
