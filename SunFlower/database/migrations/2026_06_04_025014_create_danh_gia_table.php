<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id();
            $table->char('makh', 10);
            $table->char('masp', 10);
            $table->char('madon', 10);
            $table->integer('so_sao');
            $table->text('binh_luan')->nullable();
            $table->timestamps();

            // Khóa ngoại liên kết đúng với kiểu char(10) của bạn
            $table->foreign('makh')->references('makh')->on('khachhang')->onDelete('cascade');
            $table->foreign('masp')->references('masp')->on('sanpham')->onDelete('cascade');
            $table->foreign('madon')->references('madon')->on('donhang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_gia');
    }
};