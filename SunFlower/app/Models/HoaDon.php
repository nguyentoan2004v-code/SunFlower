<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

    protected $table = 'hoadon';
    protected $primaryKey = 'mahd';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['mahd', 'tongtien', 'thue', 'ngayxuat', 'ptthanhtoan', 'madon'];

    // Hóa đơn THUỘC VỀ 1 Đơn hàng (1-1)
    public function donhang()
    {
        return $this->belongsTo(DonHang::class, 'madon', 'madon');
    }
}