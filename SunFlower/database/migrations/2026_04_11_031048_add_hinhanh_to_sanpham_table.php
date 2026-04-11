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
        Schema::table('sanpham', function (Blueprint $table) {
            // Thêm cột 'hinhanh', cho phép rỗng (nullable) để tránh lỗi nếu chưa có ảnh
            $table->string('hinhanh', 255)->nullable()->after('mota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sanpham', function (Blueprint $table) {
            //
        });
    }
};
