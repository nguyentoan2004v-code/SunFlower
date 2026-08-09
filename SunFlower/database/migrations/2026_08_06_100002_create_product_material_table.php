<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BOM REFACTOR — Bảng pivot Công thức sản phẩm (BOM)
     * Liên kết N-N giữa sanpham và materials.
     * Mỗi bản ghi = "Sản phẩm X cần Y đơn vị nguyên liệu Z"
     */
    public function up(): void
    {
        Schema::create('product_material', function (Blueprint $table) {
            $table->char('product_id', 10);            // FK → sanpham.masp
            $table->unsignedBigInteger('id_nguyen_lieu'); // FK → materials.id
            $table->integer('quantity')->default(1);   // Định mức (cần bao nhiêu NL cho 1 sản phẩm)

            $table->primary(['product_id', 'id_nguyen_lieu']);
            $table->foreign('product_id')->references('masp')->on('sanpham')->onDelete('cascade');
            $table->foreign('id_nguyen_lieu')->references('id')->on('materials')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_material');
    }
};
