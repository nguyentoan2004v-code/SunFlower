<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHangNguyenLieu extends Model
{
    protected $table = 'chitiet_donhang_nguyenlieu';

    protected $fillable = ['id_chitiet_donhang', 'id_nguyen_lieu', 'soluong_dung'];

    public function chiTietDonHang()
    {
        return $this->belongsTo(ChiTietDonHang::class, 'id_chitiet_donhang');
    }

    public function nguyenLieu()
    {
        return $this->belongsTo(NguyenLieu::class, 'id_nguyen_lieu');
    }
}
