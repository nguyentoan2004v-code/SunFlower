<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\NhanVien;
use App\Models\VaiTro;
use App\Models\VaiTroNhanVien;
use App\Models\LichSuGia;
use App\Models\LoHang;
use App\Models\PhieuHuyHang;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. VAI TRÒ & NHÂN SỰ
        $vaitros = [
            ['mavt' => 'VT00000001', 'tenvt' => 'Quản lý Cửa hàng', 'mota' => 'Full quyền hệ thống'],
            ['mavt' => 'VT00000002', 'tenvt' => 'Nhân viên Bán hàng', 'mota' => 'Thao tác đơn hàng'],
        ];
        foreach ($vaitros as $vt) VaiTro::create($vt);

        $nhanviens = [
            [
                'manv' => 'NV00000001', 
                'hoten' => 'Trần Văn Sếp', 
                'email' => 'sep@sunflower.vn', 
                'ngaysinh' => '1990-05-20', 
                'sdt' => '0900111222', 
                'luong' => 25000000, 
                'maquanly' => null,
                'password' => Hash::make('123456')
            ],
            [
                'manv' => 'NV00000002', 
                'hoten' => 'Nguyễn Thị Bán Hoa', 
                'email' => 'nhanvien@sunflower.vn',
                'ngaysinh' => '1998-10-15', 
                'sdt' => '0900333444', 
                'luong' => 12000000, 
                'maquanly' => 'NV00000001',
                'password' => Hash::make('123456')
            ],
        ];

        foreach ($nhanviens as $nv) NhanVien::create($nv);

        VaiTroNhanVien::create(['manv' => 'NV00000001', 'mavt' => 'VT00000001']);
        VaiTroNhanVien::create(['manv' => 'NV00000002', 'mavt' => 'VT00000002']);

        // 2. DANH MỤC
        $danhmucs = [
            ['madm' => 'DM00000001', 'tendm' => 'Hoa Hồng Tình Yêu'],
            ['madm' => 'DM00000002', 'tendm' => 'Lan Hồ Điệp'],
            ['madm' => 'DM00000003', 'tendm' => 'Hoa Khai Trương'],
        ];
        foreach ($danhmucs as $dm) DanhMuc::create($dm);

        // 3. SẢN PHẨM (Tổng cộng 5 sp)
        $sanphams = [
            ['masp' => 'SP00000001', 'tensp' => 'Bó Hồng 99 Bông', 'giaban' => 990000, 'mota' => 'Biểu tượng tình yêu vĩnh cửu', 'hinhanh' => 'bo-hong-99.jpg', 'madm' => 'DM00000001'],
            ['masp' => 'SP00000002', 'tensp' => 'Chậu Lan Hồ Điệp Trắng', 'giaban' => 2500000, 'mota' => 'Sang trọng và thanh cao', 'hinhanh' => 'lan-trang.jpg', 'madm' => 'DM00000002'],
            ['masp' => 'SP00000003', 'tensp' => 'Bó Hướng Dương Hy Vọng', 'giaban' => 450000, 'mota' => 'Tươi sáng và rực rỡ', 'hinhanh' => 'huong-duong.jpg', 'madm' => 'DM00000001'],
            ['masp' => 'SP00000004', 'tensp' => 'Kệ Hoa Khai Trương Hồng Phát', 'giaban' => 1500000, 'mota' => 'Chúc mừng thành công rực rỡ', 'hinhanh' => 'ke-khai-truong.jpg', 'madm' => 'DM00000003'],
            ['masp' => 'SP00000005', 'tensp' => 'Giỏ Hoa Tulip Hà Lan', 'giaban' => 850000, 'mota' => 'Vẻ đẹp tinh tế từ Châu Âu', 'hinhanh' => 'tulip.jpg', 'madm' => 'DM00000001'],
        ];
        foreach ($sanphams as $sp) SanPham::create($sp);

        // 4. LỊCH SỬ GIÁ (Ví dụ cho SP00000002)
        LichSuGia::create(['magia' => 'LG00000001', 'masp' => 'SP00000002', 'giaban' => 2200000, 'ngay_ap_dung' => $now->copy()->subMonth()]);
        LichSuGia::create(['magia' => 'LG00000002', 'masp' => 'SP00000002', 'giaban' => 2500000, 'ngay_ap_dung' => $now]);

        // 5. LÔ HÀNG (Mỗi sản phẩm 1 lô cho đủ kho)
        foreach ($sanphams as $index => $sp) {
            LoHang::create([
                'malo' => 'LH' . str_pad($index + 1, 8, '0', STR_PAD_LEFT),
                'masp' => $sp['masp'],
                'soluong_nhap' => 100,
                'soluong_ton' => 100,
                'ngaynhap' => $now,
                'ngayhethan' => $now->copy()->addDays(5)
            ]);
        }
    }
}