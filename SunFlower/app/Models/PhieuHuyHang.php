<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuHuyHang extends Model
{
    use HasFactory;

    protected $table = 'phieu_huy_hang';

    protected $fillable = [
        'ma_phieu_huy',
        'manv_lap',
        'manv_duyet',
        'ghi_chu_chung',
        'trang_thai',
    ];

    public function nguoiLap()
    {
        return $this->belongsTo(NhanVien::class, 'manv_lap', 'manv');
    }

    public function nguoiDuyet()
    {
        return $this->belongsTo(NhanVien::class, 'manv_duyet', 'manv');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhieuHuy::class, 'id_phieu_huy');
    }
}
