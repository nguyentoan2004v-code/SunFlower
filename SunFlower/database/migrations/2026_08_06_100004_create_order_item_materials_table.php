<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BOM REFACTOR — Bảng bản sao công thức nguyên liệu cho từng dòng đơn hàng
     * Khi khách đặt hàng, công thức BOM gốc được copy sang đây.
     * Nếu khách muốn đổi hoa, nhân viên sửa ở đây mà KHÔNG ảnh hưởng BOM gốc.
     */
    public function up(): void
    {
        Schema::create('order_item_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_chitiet_donhang'); // FK → chitiet_donhang.id
            $table->unsignedBigInteger('id_nguyen_lieu');      // FK → materials.id
            $table->integer('quantity');                     // Số lượng NL dùng thực tế cho dòng này

            $table->foreign('id_chitiet_donhang')->references('id')->on('chitiet_donhang')->onDelete('cascade');
            $table->foreign('id_nguyen_lieu')->references('id')->on('materials')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_item_materials');
    }
};
