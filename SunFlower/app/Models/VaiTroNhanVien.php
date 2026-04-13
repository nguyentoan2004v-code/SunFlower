<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTroNhanVien extends Model
{
    protected $table = 'vaitro_nhanvien';
    public $incrementing = false; 
    
    protected $fillable = ['manv', 'mavt'];
}