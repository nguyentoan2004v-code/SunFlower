<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Đổi tên các bảng
        DB::statement("RENAME TABLE materials TO nguyen_lieu");
        DB::statement("RENAME TABLE product_material TO sanpham_nguyenlieu");
        DB::statement("RENAME TABLE order_item_materials TO chitiet_donhang_nguyenlieu");
        DB::statement("RENAME TABLE inventory_logs TO lichsu_kho");

        // 2. Đổi tên cột trong nguyen_lieu
        DB::statement("ALTER TABLE nguyen_lieu 
            RENAME COLUMN name TO ten_nl, 
            RENAME COLUMN unit TO dvt, 
            RENAME COLUMN physical_stock TO tonkho_thucte, 
            RENAME COLUMN reserved_stock TO tonkho_datruoc, 
            RENAME COLUMN min_stock TO tonkho_toithieu, 
            RENAME COLUMN cost_price TO gia_von");

        // 3. Đổi tên cột trong sanpham_nguyenlieu
        DB::statement("ALTER TABLE sanpham_nguyenlieu 
            RENAME COLUMN product_id TO masp, 
            RENAME COLUMN material_id TO id_nguyen_lieu, 
            RENAME COLUMN quantity TO dinh_muc");

        // 4. Đổi tên cột trong chitiet_donhang_nguyenlieu
        DB::statement("ALTER TABLE chitiet_donhang_nguyenlieu 
            RENAME COLUMN order_detail_id TO id_chitiet_donhang, 
            RENAME COLUMN material_id TO id_nguyen_lieu, 
            RENAME COLUMN quantity TO soluong_dung");

        // 5. Đổi tên cột trong lichsu_kho
        DB::statement("ALTER TABLE lichsu_kho 
            RENAME COLUMN material_id TO id_nguyen_lieu, 
            RENAME COLUMN type TO loai_gd, 
            RENAME COLUMN quantity TO soluong, 
            RENAME COLUMN note TO ghichu");

        // 6. Đổi tên cột trong phieu_nhap_kho
        DB::statement("ALTER TABLE phieu_nhap_kho 
            RENAME COLUMN code TO maphieu, 
            RENAME COLUMN supplier_id TO id_nhacungcap, 
            RENAME COLUMN total_amount TO tongtien, 
            RENAME COLUMN note TO ghichu, 
            RENAME COLUMN status TO trangthai");

        // 7. Đổi tên cột trong chi_tiet_phieu_nhap
        DB::statement("ALTER TABLE chi_tiet_phieu_nhap 
            RENAME COLUMN phieu_nhap_id TO id_phieu_nhap, 
            RENAME COLUMN material_id TO id_nguyen_lieu, 
            RENAME COLUMN quantity TO soluong, 
            RENAME COLUMN unit_price TO dongia, 
            RENAME COLUMN subtotal TO thanhtien, 
            RENAME COLUMN lot_number TO malo, 
            RENAME COLUMN expiry_date TO hsd");

        // 8. Đổi tên cột trong lo_nguyen_lieu
        DB::statement("ALTER TABLE lo_nguyen_lieu 
            RENAME COLUMN material_id TO id_nguyen_lieu, 
            RENAME COLUMN phieu_nhap_id TO id_phieu_nhap, 
            RENAME COLUMN chi_tiet_phieu_nhap_id TO id_chitiet_phieu_nhap, 
            RENAME COLUMN lot_number TO malo, 
            RENAME COLUMN initial_quantity TO soluong_bandau, 
            RENAME COLUMN current_quantity TO soluong_hientai, 
            RENAME COLUMN unit_price TO dongia, 
            RENAME COLUMN expiry_date TO hsd, 
            RENAME COLUMN status TO trangthai");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is just the exact opposite. Assuming not needed for this manual task.
    }
};
