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

    protected $fillable = ['manv', 'hoten', 'sdt', 'chucvu', 'ma_ql'];

    // 1. TỰ THAM CHIẾU: Nhân viên này có Sếp (Quản lý) là ai?
    public function quanly()
    {
        return $this->belongsTo(NhanVien::class, 'ma_ql', 'manv');
    }

    // 2. TỰ THAM CHIẾU: Quản lý này đang quản lý NHỮNG Nhân viên nào?
    public function capduoi()
    {
        return $this->hasMany(NhanVien::class, 'ma_ql', 'manv');
    }

    // 3. QUAN HỆ N-N: 1 Nhân viên làm NHIỀU Ca (thông qua bảng phancong)
    public function lichlamviecs()
    {
        return $this->belongsToMany(LichLamViec::class, 'phancong', 'manv', 'malich')
                    ->withPivot('nhiemvu', 'trangthai') // Các cột phụ trong bảng phân công
                    ->withTimestamps();
    }
    public function vaitros()
    {
        return $this->belongsToMany(VaiTro::class, 'vaitro_nhanvien', 'manv', 'mavt')
                    ->withTimestamps();
    }
}