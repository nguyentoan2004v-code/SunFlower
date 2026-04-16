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
        Schema::table('khachhang', function (Blueprint $table) {
        // Cho phép cột diachi được để trống (nullable)
        $table->string('diachi')->nullable()->change();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khachhang', function (Blueprint $table) {
        // Nếu rollback, cột diachi sẽ quay lại trạng thái bắt buộc (không được null)
        $table->string('diachi')->nullable(false)->change();
        });
    }
};
