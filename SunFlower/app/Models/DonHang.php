<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;

    protected $table = 'donhang';
    protected $primaryKey = 'madon';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['madon', 'ngaydat', 'tongtien', 'trangthai', 'makh', 
    'sdt_nhan', 'diachi_giao', 'ghichu'];

    // Đơn hàng THUỘC VỀ 1 Khách hàng (N-1)
    public function khachhang()
    {
        return $this->belongsTo(KhachHang::class, 'makh', 'makh');
    }

    // 1 Đơn hàng có ĐÚNG 1 Hóa đơn (1-1)
    public function hoadon()
    {
        // hasOne(Tên_Model_Con, 'Tên_khóa_ngoại', 'Tên_khóa_chính')
        return $this->hasOne(HoaDon::class, 'madon', 'madon');
    }
    // Quan hệ N-N: 1 Đơn hàng có NHIỀU Sản phẩm (thông qua bảng trung gian chitietdonhang)
    public function sanphams()
    {
        return $this->belongsToMany(SanPham::class, 'chitiet_donhang', 'madon', 'masp')
                    ->withPivot('soluong', 'giaban') // Lấy thêm cột phụ từ bảng trung gian
                    ->withTimestamps();
    }
}