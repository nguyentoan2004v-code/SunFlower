<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHangNguyenLieuLo extends Model
{
    use HasFactory;

    protected $table = 'donhang_nguyenlieu_lo';

    protected $fillable = [
        'id_chitiet_donhang_nguyenlieu',
        'id_lo',
        'soluong',
    ];

    public function orderItemMaterial()
    {
        return $this->belongsTo(ChiTietDonHangNguyenLieu::class, 'id_chitiet_donhang_nguyenlieu');
    }

    public function loNguyenLieu()
    {
        return $this->belongsTo(LoNguyenLieu::class, 'id_lo');
    }
}
