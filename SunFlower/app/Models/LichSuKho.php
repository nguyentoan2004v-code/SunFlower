<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LichSuKho extends Model
{
    protected $table = 'lichsu_kho';

    protected $fillable = ['id_nguyen_lieu', 'loai_gd', 'soluong', 'ghichu', 'manv'];

    public function nguyenLieu()
    {
        return $this->belongsTo(NguyenLieu::class, 'id_nguyen_lieu');
    }

    public function nhanvien()
    {
        return $this->belongsTo(NhanVien::class, 'manv', 'manv');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->loai_gd) {
            'import'         => 'Nhập kho',
            'export'         => 'Xuất kho',
            'waste'          => 'Xuất hủy',
            'order_reserve'  => 'Giữ cho đơn hàng',
            'order_complete' => 'Hoàn thành đơn',
            'order_cancel'   => 'Hủy đơn (nhả hàng)',
            default          => $this->loai_gd,
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match($this->loai_gd) {
            'import'         => 'success',
            'export'         => 'info',
            'waste'          => 'danger',
            'order_reserve'  => 'warning',
            'order_complete' => 'primary',
            'order_cancel'   => 'secondary',
            default          => 'dark',
        };
    }
}
