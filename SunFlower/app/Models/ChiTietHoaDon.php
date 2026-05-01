<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    protected $table = 'chitiet_hoadon';
    public $incrementing = false; 

    protected $fillable = ['mahd', 'masp', 'soluong', 'dongia', 'tensp'];

     // Chi tiết hóa đơn THUỘC VỀ 1 Hóa đơn (N-1)

    public function hoadon()
    {
        return $this->belongsTo(HoaDon::class, 'mahd', 'mahd');
    }
}