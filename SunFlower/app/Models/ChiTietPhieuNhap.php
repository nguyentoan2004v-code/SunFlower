<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuNhap extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_phieu_nhap';

    protected $fillable = [
        'id_phieu_nhap',
        'id_nguyen_lieu',
        'soluong',
        'dongia',
        'thanhtien',
        'malo',
        'hsd'
    ];

    public function phieuNhap()
    {
        return $this->belongsTo(PhieuNhapKho::class, 'id_phieu_nhap');
    }

    public function nguyenLieu()
    {
        return $this->belongsTo(NguyenLieu::class, 'id_nguyen_lieu');
    }
}
