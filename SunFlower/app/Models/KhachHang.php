<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class KhachHang extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'khachhang';
    protected $primaryKey = 'makh';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['makh', 'hoten', 'email', 'sdt', 'diachi', 'password'];

    protected $hidden = ['password'];
}