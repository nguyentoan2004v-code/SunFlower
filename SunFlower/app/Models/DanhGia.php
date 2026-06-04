<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    use HasFactory;

    protected $table = 'danh_gia';

    protected $fillable = [
        'makh',
        'masp',
        'madon',
        'so_sao',
        'binh_luan',
        'phan_hoi',   
        'trang_thai',
    ];

    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'makh', 'makh');
    }

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'masp', 'masp');
    }

    public function donHang()
    {
        return $this->belongsTo(DonHang::class, 'madon', 'madon');
    }
}