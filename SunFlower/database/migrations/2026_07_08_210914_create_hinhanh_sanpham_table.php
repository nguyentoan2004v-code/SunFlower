<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hinhanh_sanpham', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng

            // Khóa ngoại liên kết đến bảng sanpham
            $table->char('masp', 10);
            $table->foreign('masp')->references('masp')->on('sanpham')->onDelete('cascade');

            // Đường dẫn URL ảnh (Cloudinary)
            $table->string('duong_dan', 500);

            // Thứ tự hiển thị (0, 1, 2, 3...)
            $table->tinyInteger('thu_tu')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hinhanh_sanpham');
    }
};
