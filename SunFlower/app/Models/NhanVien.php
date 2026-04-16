<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    use HasFactory;

    protected $table = 'nhanvien';
    protected $primaryKey = 'manv';
    public $incrementing = false;
    protected $keyType = 'string';

    // 1. ĐÃ SỬA: Xóa 'chucvu', thêm 'ngaysinh', 'luong', đổi 'ma_ql' thành 'maquanly'
    protected $fillable = ['manv', 'hoten', 'ngaysinh', 'sdt', 'luong', 'maquanly'];

    
    public function quanly()
    {
        return $this->belongsTo(NhanVien::class, 'maquanly', 'manv');
    }

    public function capduoi()
    {
        return $this->hasMany(NhanVien::class, 'maquanly', 'manv');
    }

    // 3. QUAN HỆ N-N: Giữ nguyên vì đã chuẩn
    public function lichlamviecs()
    {
        return $this->belongsToMany(LichLamViec::class, 'phancong', 'manv', 'malich')
                    ->withPivot('nhiemvu', 'trangthai')
                    ->withTimestamps();
    }

    // 4. QUAN HỆ N-N VỚI VAI TRÒ: Giữ nguyên vì đã chuẩn
    public function vaitros()
    {
        return $this->belongsToMany(VaiTro::class, 'vaitro_nhanvien', 'manv', 'mavt')
                    ->withTimestamps();
    }
}