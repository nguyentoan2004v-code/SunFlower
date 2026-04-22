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
        Schema::table('danhmuc', function (Blueprint $table) {
            // Thêm cột hinhanh, cho phép rỗng (nullable) để tránh lỗi dữ liệu cũ
        $table->string('hinhanh')->nullable()->after('tendm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('danhmuc', function (Blueprint $table) {
            $table->dropColumn('hinhanh');
        });
    }
};
