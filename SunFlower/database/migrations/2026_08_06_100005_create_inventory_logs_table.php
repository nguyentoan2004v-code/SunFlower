<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BOM REFACTOR — Bảng lịch sử biến động kho (Inventory Logs)
     * Ghi nhận mọi thao tác ảnh hưởng đến tồn kho nguyên liệu:
     * - import: Nhập kho
     * - export: Xuất kho thông thường
     * - waste: Xuất hủy (hàng hỏng, hết hạn...)
     * - order_reserve: Giữ hàng cho đơn mới
     * - order_complete: Trừ kho khi đơn hoàn thành
     * - order_cancel: Nhả hàng khi đơn bị hủy
     */
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguyen_lieu');
            $table->string('type', 20); // import, export, waste, order_reserve, order_complete, order_cancel
            $table->integer('quantity');                       // Số lượng thay đổi (dương = tăng, âm = giảm)
            $table->string('note', 500)->nullable();          // Ghi chú / Lý do
            $table->char('manv', 10)->nullable();              // Nhân viên thực hiện (nullable cho system auto)

            $table->foreign('id_nguyen_lieu')->references('id')->on('materials')->onDelete('restrict');
            $table->foreign('manv')->references('manv')->on('nhanvien')->onDelete('set null');

            $table->index(['id_nguyen_lieu', 'type']);           // Index cho báo cáo lọc theo NL + loại
            $table->index('created_at');                      // Index cho lọc theo thời gian
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
