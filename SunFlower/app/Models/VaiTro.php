<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vaitro';
    protected $primaryKey = 'mavt';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['mavt', 'tenvt', 'mota'];

    // 1 Vai trò có nhiều Nhân viên (N-N)
    public function nhanviens()
    {
        return $this->belongsToMany(NhanVien::class, 'vaitro_nhanvien', 'mavt', 'manv');
    }
}