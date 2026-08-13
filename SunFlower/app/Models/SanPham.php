<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SanPham extends Model
{
    use HasFactory, SoftDeletes;

    // 1. Quy tắc 4 dòng cho khóa chính
    protected $table = 'sanpham';
    protected $primaryKey = 'masp';
    public $incrementing = false;
    protected $keyType = 'string';

    // 2. Khai báo các cột được phép nhập
    protected $fillable = ['masp', 'tensp', 'giaban', 'mota','mota_chitiet','hinhanh', 'giakm', 'madm', 'tonkho_toithieu'];

    // 3. THIẾT LẬP MỐI QUAN HỆ (1 Sản phẩm THUỘC VỀ 1 Danh mục)
    public function danhmuc()
    {
        // belongsTo(Tên_Model_Cha, 'Tên_khóa_ngoại', 'Tên_khóa_chính_của_cha')
        return $this->belongsTo(DanhMuc::class, 'madm', 'madm');
    }
    // Quan hệ N-N: 1 Sản phẩm nằm trong NHIỀU Đơn hàng
    public function donhangs()
    {
        return $this->belongsToMany(DonHang::class, 'chitiet_donhang', 'masp', 'madon')
                    ->withPivot('soluong', 'dongia')
                    ->withTimestamps();
    }
    // 1 Sản phẩm có NHIỀU mốc Lịch sử giá
    public function lichsugias()
    {
        return $this->hasMany(LichSuGia::class, 'masp', 'masp');
    }



    // 1 Sản phẩm có NHIỀU ảnh phụ (Gallery)
    public function hinhAnhPhu()
    {
        return $this->hasMany(HinhAnhSanPham::class, 'masp', 'masp')->orderBy('thu_tu');
    }

    /**
     * Quan hệ N-N: 1 Sản phẩm có NHIỀU Nguyên liệu cấu thành (BOM)
     */
    public function nguyenLieus()
    {
        return $this->belongsToMany(NguyenLieu::class, 'sanpham_nguyenlieu', 'masp', 'id_nguyen_lieu')
                    ->withPivot('dinh_muc');
    }

    /**
     * TỒN KHO ĐỘNG: Tính số lượng sản phẩm có thể tạo ra dựa trên nguyên liệu khả dụng.
     * Công thức: min( floor( (physical_stock - reserved_stock) / định_mức ) ) cho mỗi NL trong BOM.
     * Trả về 0 nếu SP chưa có công thức BOM.
     */
    public function getAvailableQuantityAttribute(): int
    {
        // Lazy load nguyenLieus nếu chưa được eager-load
        $materials = $this->relationLoaded('nguyenLieus') ? $this->nguyenLieus : $this->nguyenLieus()->get();

        // SP chưa có BOM → không thể tính tồn kho → trả về 0
        if ($materials->isEmpty()) {
            return 0;
        }

        $maxQuantity = PHP_INT_MAX;

        foreach ($materials as $material) {
            $dinhMuc = $material->pivot->dinh_muc;

            // Tránh chia cho 0
            if ($dinhMuc <= 0) continue;

            $availableStock = max(0, $material->tonkho_thucte - $material->tonkho_datruoc);
            $canMake = (int) floor($availableStock / $dinhMuc);

            $maxQuantity = min($maxQuantity, $canMake);
        }

        // Nếu tất cả định mức = 0 thì trả về 0
        return $maxQuantity === PHP_INT_MAX ? 0 : $maxQuantity;
    }

    protected static function booted()
    {
        // Tự động trim khoảng trắng cho masp khi lấy dữ liệu ra
        static::retrieved(function ($model) {
            $model->masp = trim($model->masp);
        });
    }
}