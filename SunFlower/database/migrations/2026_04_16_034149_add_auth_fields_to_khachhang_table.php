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
            // Xóa đoạn ->after() đi để tự động thêm vào cuối bảng
            $table->string('email')->unique();
            $table->string('password');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khachhang', function (Blueprint $table) {
            //
        });
    }
};
