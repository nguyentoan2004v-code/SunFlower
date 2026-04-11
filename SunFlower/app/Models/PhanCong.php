<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanCong extends Model
{
    use HasFactory;

    protected $table = 'phancong';
    public $incrementing = false; 
    // Bỏ qua primaryKey để tránh lỗi khóa kép

    protected $fillable = ['manv', 'malich', 'nhiemvu', 'trangthai'];

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'manv', 'manv');
    }

    public function lichlamviec()
    {
        return $this->belongsTo(LichLamViec::class, 'malich', 'malich');
    }
}