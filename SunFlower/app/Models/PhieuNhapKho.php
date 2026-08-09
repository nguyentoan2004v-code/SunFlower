<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuNhapKho extends Model
{
    use HasFactory;

    protected $table = 'phieu_nhap_kho';

    protected $fillable = [
        'maphieu',
        'id_nhacungcap',
        'manv',
        'tongtien',
        'ghichu',
        'trangthai'
    ];

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhieuNhap::class, 'id_phieu_nhap');
    }

    public function nhaCungCap()
    {
        return $this->belongsTo(NhaCungCap::class, 'id_nhacungcap');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'manv', 'manv');
    }
}
