<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BOM REFACTOR — Bảng Nguyên liệu (Materials)
     * Trung tâm của hệ thống kho mới. Mỗi nguyên liệu có:
     * - physical_stock: Số lượng thực tế trong kho
     * - reserved_stock: Số lượng đã giữ cho đơn hàng chưa giao
     * - Tồn kho khả dụng = physical_stock - reserved_stock
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                      // Tên nguyên liệu (Hoa hồng đỏ, Giấy gói kraft...)
            $table->string('unit', 20)->default('cành');       // Đơn vị tính (cành, tờ, xốp, cuộn, cái)
            $table->integer('physical_stock')->default(0);     // Tồn thực tế (số lượng vật lý trong kho)
            $table->integer('reserved_stock')->default(0);     // Đang giữ cho đơn hàng chưa giao
            $table->decimal('cost_price', 12, 2)->default(0);  // Giá vốn trung bình
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
