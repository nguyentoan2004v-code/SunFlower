<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuHuyHang extends Model
{
    protected $table = 'phieu_huy_hang';
    protected $primaryKey = 'maphieu';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['maphieu', 'malo', 'masp', 'soluong_huy', 'ngayhuy', 'lydo'];

    public function lohang()
    {
        return $this->belongsTo(LoHang::class, 'malo', 'malo');
    }
}
