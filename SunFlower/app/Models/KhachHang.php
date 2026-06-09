<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class KhachHang extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'khachhang';
    protected $primaryKey = 'makh';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['makh', 'hoten', 'email', 'sdt', 'diachi', 'password'];

    protected $hidden = ['password'];
    
    public function hangThanhVien()
    {
        return $this->belongsTo(HangThanhVien::class, 'hang_thanh_vien_id', 'id');
    }

    // Quan hệ với Ví Voucher (Các voucher khách đã đổi)
    public function vouchers()
    {
        return $this->belongsToMany(Voucher::class, 'khachhang_voucher', 'makh', 'mavoucher')
                    ->withPivot('trang_thai', 'ngay_doi')
                    ->withTimestamps();
    }

    // Quan hệ với Lịch Sử Điểm
    public function lichSuDiems()
    {
        return $this->hasMany(LichSuDiem::class, 'makh', 'makh');
    }
}