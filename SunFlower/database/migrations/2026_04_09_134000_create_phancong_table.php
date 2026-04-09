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
            $table->string('tencongviec', 50);
            $table->string('dacta', 255);

            // Thiết lập Khóa chính kép (1 người ở 1 ca chỉ có 1 dòng dữ liệu)
            $table->primary(['manv', 'maca']);

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
