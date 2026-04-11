<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;

    protected $table = 'khachhang';
    protected $primaryKey = 'makh';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['makh', 'hoten', 'ngaysinh', 'sdt', 'diachi'];

    // 1 Khách hàng có NHIỀU Đơn hàng (1-N)
    public function donhangs()
    {
        return $this->hasMany(DonHang::class, 'makh', 'makh');
    }
}