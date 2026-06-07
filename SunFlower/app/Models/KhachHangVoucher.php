<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHangVoucher extends Model
{
    use HasFactory;

    protected $table = 'khachhang_voucher';
    public $timestamps = true;

    protected $fillable = [
        'makh',
        'mavoucher',
        'trang_thai',
        'ngay_doi'
    ];

    // Quan hệ ngược lại với Khách Hàng
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'makh', 'makh');
    }

    // Quan hệ ngược lại với Voucher
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'mavoucher', 'mavoucher');
    }
}