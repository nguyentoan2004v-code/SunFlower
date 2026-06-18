<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\KhachHangResetPassword;

class KhachHang extends Authenticatable implements CanResetPasswordContract
{
    use HasApiTokens, Notifiable, CanResetPassword;

    protected $table = 'khachhang';
    protected $primaryKey = 'makh';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['makh', 'hoten', 'email', 'sdt', 'diachi', 'password', 'ngaysinh'];

    protected $hidden = ['password'];

    /**
     * Gửi email đặt lại mật khẩu bằng tiếng Việt.
     * Override để dùng route name đúng ('password.reset.khachhang')
     * và KHÔNG gọi setRememberToken() — bảng khachhang không có cột remember_token.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new KhachHangResetPassword($token));
    }

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