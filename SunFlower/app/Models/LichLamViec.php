<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichLamViec extends Model
{
    use HasFactory;

    protected $table = 'lichlamviec';
    protected $primaryKey = 'malich';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['malich', 'ngaylam', 'tenca', 'giovao', 'giora'];

    // QUAN HỆ N-N: 1 Ca làm việc có NHIỀU Nhân viên trực
    public function nhanviens()
    {
        return $this->belongsToMany(NhanVien::class, 'phancong', 'malich', 'manv')
                    ->withPivot('nhiemvu', 'trangthai')
                    ->withTimestamps();
    }
}