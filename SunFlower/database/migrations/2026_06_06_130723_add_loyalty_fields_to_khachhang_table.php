<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('khachhang', function (Blueprint $table) {
            $table->unsignedBigInteger('hang_thanh_vien_id')->nullable();
            $table->integer('tong_chi_tieu')->default(0);
            $table->integer('diem_thuong')->default(0);

            // Tạo khóa ngoại liên kết với bảng hang_thanh_vien
            $table->foreign('hang_thanh_vien_id')->references('id')->on('hang_thanh_vien')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('khachhang', function (Blueprint $table) {
            $table->dropForeign(['hang_thanh_vien_id']);
            $table->dropColumn(['hang_thanh_vien_id', 'tong_chi_tieu', 'diem_thuong']);
        });
    }
};
