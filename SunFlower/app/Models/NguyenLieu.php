<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguyenLieu extends Model
{
    use HasFactory;

    protected $table = 'nguyen_lieu';

    protected $fillable = [
        'ten_nl',
        'dvt',
        'tonkho_thucte',
        'tonkho_datruoc',
        'tonkho_toithieu',
        'gia_von',
    ];

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->tonkho_thucte - $this->tonkho_datruoc);
    }

    public function sanphams()
    {
        return $this->belongsToMany(SanPham::class, 'sanpham_nguyenlieu', 'id_nguyen_lieu', 'masp')
                    ->withPivot('dinh_muc');
    }

    public function lichSuKhos()
    {
        return $this->hasMany(LichSuKho::class, 'id_nguyen_lieu');
    }

    public function chiTietDonHangNguyenLieus()
    {
        return $this->hasMany(ChiTietDonHangNguyenLieu::class, 'id_nguyen_lieu');
    }
}
