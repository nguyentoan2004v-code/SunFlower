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
            // ĐÃ BỎ COMMENT: Cột này đã được khai báo ở bảng sanpham gốc nên không cần add thêm nữa
            // $table->string('hinhanh', 255)->nullable()->after('mota');
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
