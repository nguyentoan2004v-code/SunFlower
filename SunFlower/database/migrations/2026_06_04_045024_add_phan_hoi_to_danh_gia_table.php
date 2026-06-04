<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('danh_gia', function (Blueprint $table) {
            $table->text('phan_hoi')->nullable()->after('binh_luan'); // Lưu phản hồi của Admin
            $table->boolean('trang_thai')->default(1)->after('phan_hoi'); // 1: Hiển thị, 0: Ẩn
        });
    }

    public function down(): void
    {
        Schema::table('danh_gia', function (Blueprint $table) {
            $table->dropColumn(['phan_hoi', 'trang_thai']);
        });
    }
};