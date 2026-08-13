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
        Schema::table('phieu_huy_hang', function (Blueprint $table) {
            $table->index('trang_thai');
        });

        Schema::table('lo_nguyen_lieu', function (Blueprint $table) {
            $table->index('soluong_hientai');
            $table->index('trangthai');
            $table->index('hsd');
        });

        Schema::table('lichsu_kho', function (Blueprint $table) {
            $table->index('loai_gd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phieu_huy_hang', function (Blueprint $table) {
            $table->dropIndex(['trang_thai']);
        });

        Schema::table('lo_nguyen_lieu', function (Blueprint $table) {
            $table->dropIndex(['soluong_hientai']);
            $table->dropIndex(['trangthai']);
            $table->dropIndex(['hsd']);
        });

        Schema::table('lichsu_kho', function (Blueprint $table) {
            $table->dropIndex(['loai_gd']);
        });
    }
};
