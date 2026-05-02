<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichLamViec extends Model
{
    use HasFactory;

    protected $table = 'lichlamviec';
    
    // 1. Cực kỳ quan trọng: Báo cho Laravel biết khóa chính là 'maca' chứ không phải 'id' hay 'malich'
    protected $primaryKey = 'maca';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['maca', 'ngaynghi', 'giolam', 'giotan'];

    // 2. Định nghĩa lại quan hệ nhiều-nhiều với NhanVien
   public function nhanviens()
    {
        return $this->belongsToMany(NhanVien::class, 'phancong', 'maca', 'manv')
                    ->withPivot('ngaylam') 
                    ->withTimestamps();
    }
}