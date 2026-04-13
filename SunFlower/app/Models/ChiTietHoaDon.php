<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    protected $table = 'chitiet_hoadon';
    public $incrementing = false; 

    protected $fillable = ['mahd', 'masp', 'soluong', 'dongia'];

    public function hoadon()
    {
        return $this->belongsTo(HoaDon::class, 'mahd', 'mahd');
    }
}