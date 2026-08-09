<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phieu_huy_hang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_phieu_huy', 50)->unique();
            $table->char('manv_lap', 10);
            $table->char('manv_duyet', 10)->nullable();
            $table->text('ghi_chu_chung')->nullable();
            $table->enum('trang_thai', ['Chờ duyệt', 'Đã duyệt', 'Từ chối'])->default('Chờ duyệt');
            $table->timestamps();

            // Foreign keys
            $table->foreign('manv_lap')->references('manv')->on('nhanvien')->onDelete('restrict');
            $table->foreign('manv_duyet')->references('manv')->on('nhanvien')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_huy_hang');
    }
};
