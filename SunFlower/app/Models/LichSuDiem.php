<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichSuDiem extends Model
{
    use HasFactory;

    protected $table = 'lich_su_diem';

    protected $fillable = [
        'makh', 
        'loai_giao_dich', 
        'so_diem', 
        'mo_ta'
    ];

    // 1 Lịch sử điểm thuộc về 1 khách hàng
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'makh', 'makh');
    }
}