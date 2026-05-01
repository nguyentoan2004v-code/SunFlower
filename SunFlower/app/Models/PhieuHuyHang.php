<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuHuyHang extends Model
{
    protected $table = 'phieu_huy_hang';
    protected $primaryKey = 'maphieu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['maphieu', 'malo', 'masp','manv', 'soluong_huy', 'ngayhuy', 'lydo'];

    public function lohang()
    {
        return $this->belongsTo(LoHang::class, 'malo', 'malo');
    }
    public function sanpham()
    {
        return $this->belongsTo(SanPham::class, 'masp', 'masp');
    }

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'manv', 'manv');
    }
    protected static function booted()
    {
        static::retrieved(function ($model) {
            $model->maphieu = trim($model->maphieu);
            $model->malo = trim($model->malo);
            $model->masp = trim($model->masp);
        });
    }
}
