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
            ['madm' => 'DM00000001', 'tendm' => 'Hoa Hồng Tình Yêu','hinhanh' => 'category/nenhoahong.png'],
            ['madm' => 'DM00000002', 'tendm' => 'Lan Hồ Điệp','hinhanh' => 'category/Nenlan.png'],
            ['madm' => 'DM00000003', 'tendm' => 'Hoa Khai Trương','hinhanh' => 'category/nenkhaitruong.png'],
        ];
        foreach ($danhmucs as $dm) DanhMuc::create($dm);

        // 3. SẢN PHẨM (Tổng cộng 5 sp)
        $sanphams = [
            ['masp' => 'SP00000001', 'tensp' => 'Bó Hồng 99 Bông', 'giaban' => 990000, 'mota' => 'Thiết kế lẵng hoa sang trọng làm nổi bật vẻ đẹp kiêu kỳ của 30 đóa hồng đỏ thắm cùng baby trắng. Món quà hoàn hảo mang thông điệp tình yêu nồng nhiệt.
            Sản phẩm bao gồm:
            Hoa chính: 30 Cành Hoa Hồng Đỏ (Size to).
            Hoa lá phụ: 1 Bộ hoa baby trắng và lá bạc.
            Phụ kiện: 1 Lẵng đan cao cấp kèm nơ.',
             'hinhanh' => 'products/hoahong99.png','giakm' => 890000,'madm' => 'DM00000001'],
            ['masp' => 'SP00000002', 'tensp' => 'Chậu Lan Hồ Điệp Trắng', 'giaban' => 2500000, 'mota' => 'Chậu lan hồ điệp 5 cành vươn dài thanh tao trong chậu sứ tráng men sang trọng. Tác phẩm tôn vinh vẻ đẹp vương giả, mang ý nghĩa phong thủy thịnh vượng.
            Sản phẩm bao gồm:
            Hoa chính: 5 Cành Lan Hồ Điệp loại A (Trắng/Tím/Vàng).
            Hoa lá phụ: 1 Bộ rêu phong và tiểu cảnh trang trí gốc.
            Phụ kiện: 1 Chậu sứ tráng men cao cấp.',
             'hinhanh' => 'products/lanhodiep.png', 'madm' => 'DM00000002'],
            ['masp' => 'SP00000003', 'tensp' => 'Bó Hướng Dương Hy Vọng', 'giaban' => 450000, 'mota' => 'Vòng nguyệt quế mộc mạc nhưng vô cùng nổi bật nhờ sắc vàng ươm rực rỡ của những đóa hướng dương. Mang ý nghĩa về niềm vui, sự ấm áp và năng lượng tích cực, thiết kế này rất lý tưởng để trang trí cửa ra vào, như một lời chào đón nồng nhiệt gửi đến những vị khách ghé thăm.
            Sản phẩm bao gồm:
            Hoa chính: 1 Bộ hoa hướng dương nở rộ với nhiều kích cỡ đan xen.
            Hoa lá phụ: 1 Bộ hoa nhí điểm xuyết tone vàng và đa dạng các loại cành lá xanh (dương xỉ, lá chanh...).
            Phụ kiện: 1 Vòng khung mây đan tự nhiên kèm nơ thắt bằng dây cói (raffia) mộc mạc.',
             'hinhanh' => 'products/huongduong.png', 'madm' => 'DM00000001'],
            ['masp' => 'SP00000004', 'tensp' => 'Kệ Hoa Khai Trương Hồng Phát', 'giaban' => 1500000, 'mota' => 'Kệ hoa hoành tráng kết hợp sắc vàng rực rỡ của hướng dương và đồng tiền. Mang ý nghĩa phong thủy tốt lành, thay lời chúc khai trương hồng phát gửi đến đối tác.
            Sản phẩm bao gồm:
            Hoa chính: 1 Bộ hoa hướng dương và hoa đồng tiền rực rỡ.
            Hoa lá phụ: 1 Bộ lá cọ và lan vũ nữ trang trí.
            Phụ kiện: 1 Kệ đứng cao cấp kèm băng ron chúc mừng.',
             'hinhanh' => 'products/khaitruong.png', 'madm' => 'DM00000003'],
            ['masp' => 'SP00000005', 'tensp' => 'Giỏ Hoa Tulip Hà Lan', 'giaban' => 850000, 'mota' => 'Bó hoa tinh tế với sự hòa quyện ngọt ngào giữa sắc hồng dịu dàng và trắng tinh khôi của những đóa Tulip. Tượng trưng cho tình yêu thuần khiết và sự quan tâm chân thành, đây sẽ là món quà hoàn hảo thay lời yêu thương gửi đến phái đẹp trong các dịp sinh nhật hay kỷ niệm.
            Sản phẩm bao gồm:
            Hoa chính: 1 Bó hoa Tulip nhập khẩu tone màu hồng và trắng ngọt ngào.
            Hoa lá phụ: Giữ nguyên vẻ đẹp thanh lịch với lá Tulip xanh mướt tự nhiên.
            Phụ kiện: 1 Bộ giấy gói kiếng trong suốt lót viền lượn sóng điệu đà, nơ ruy băng lụa hồng và tag thiệp xinh xắn.',
             'hinhanh' => 'products/tulip.png', 'madm' => 'DM00000001'],
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