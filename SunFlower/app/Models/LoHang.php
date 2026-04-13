<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoHang extends Model
{
    protected $table = 'lo_hang';
    protected $primaryKey = 'malo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['malo', 'masp', 'soluong_nhap', 'soluong_ton', 'ngaynhap', 'ngayhethan'];

    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'masp', 'masp');
    }

    // Một lô hàng có thể có nhiều phiếu hủy nếu hoa bị hỏng
    public function phieuhuyhangs()
    {
        return $this->hasMany(PhieuHuyHang::class, 'malo', 'malo');
    }
}
