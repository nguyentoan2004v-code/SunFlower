<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuHuy extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phieu_huy';

    protected $fillable = [
        'id_phieu_huy',
        'id_lo_nguyen_lieu',
        'so_luong_huy',
        'ly_do_chi_tiet',
        'hinh_anh_minh_chung',
    ];

    public function phieuHuy()
    {
        return $this->belongsTo(PhieuHuyHang::class, 'id_phieu_huy');
    }

    public function loNguyenLieu()
    {
        return $this->belongsTo(LoNguyenLieu::class, 'id_lo_nguyen_lieu');
    }
}
