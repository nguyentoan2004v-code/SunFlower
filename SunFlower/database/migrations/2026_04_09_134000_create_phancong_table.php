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
        Schema::create('phancong', function (Blueprint $table) {
            $table->char('manv', 10);
            $table->char('maca', 10);
            $table->date('ngaylam');

            // Thiết lập Khóa chính kép gồm 3 cột (Cho phép nhân viên làm cùng 1 ca vào nhiều ngày khác nhau)
            $table->primary(['manv', 'maca', 'ngaylam']);

            // Thiết lập Khóa ngoại
            $table->foreign('manv')->references('manv')->on('nhanvien')->onDelete('cascade');
            $table->foreign('maca')->references('maca')->on('lichlamviec')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phancong');
    }
};
