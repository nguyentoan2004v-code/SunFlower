<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BOM REFACTOR — Thêm cột id auto-increment vào chitiet_donhang
     * Bảng cũ dùng composite PK (madon, masp) nên không thể tham chiếu
     * từ bảng order_item_materials. Cần thêm cột id đơn lẻ.
     */
    public function up(): void
    {
        Schema::table('chitiet_donhang', function (Blueprint $table) {
            // Bước 1: Xóa composite primary key cũ
            $table->dropPrimary(['madon', 'masp']);
        });

        Schema::table('chitiet_donhang', function (Blueprint $table) {
            // Bước 2: Thêm cột id auto-increment làm PK mới
            $table->id()->first();

            // Bước 3: Tạo unique index thay thế cho PK cũ (vẫn đảm bảo 1 SP không xuất hiện 2 lần trong 1 đơn)
            $table->unique(['madon', 'masp'], 'chitiet_donhang_madon_masp_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chitiet_donhang', function (Blueprint $table) {
            $table->dropUnique('chitiet_donhang_madon_masp_unique');
            $table->dropColumn('id');
        });

        Schema::table('chitiet_donhang', function (Blueprint $table) {
            $table->primary(['madon', 'masp']);
        });
    }
};
