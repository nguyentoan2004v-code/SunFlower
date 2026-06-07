<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('voucher', function (Blueprint $table) {
            // Sửa 'so_luong' thành 'soluong'
            $table->integer('diem_doi')->default(0)->after('soluong'); 
        });
    }

    public function down(): void
    {
        Schema::table('voucher', function (Blueprint $table) {
            $table->dropColumn('diem_doi');
        });
    }
};