<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HangThanhVien extends Model
{
    use HasFactory;

    protected $table = 'hang_thanh_vien';

    protected $fillable = [
        'ten_hang', 
        'chi_tieu_toi_thieu', 
        'phan_tram_giam'
    ];

    // 1 Hạng thành viên sẽ có nhiều khách hàng đạt được
    public function khachHangs()
    {
        return $this->hasMany(KhachHang::class, 'hang_thanh_vien_id', 'id');
    }
}