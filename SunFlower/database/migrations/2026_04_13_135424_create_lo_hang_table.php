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
        Schema::create('lo_hang', function (Blueprint $table) {
           $table->char('malo', 10)->primary(); // SỬA string THÀNH char
            $table->char('masp', 10); // SỬA string THÀNH char

            $table->integer('soluong_nhap');
            $table->integer('soluong_ton');
            $table->date('ngaynhap');
            $table->date('ngayhethan'); 

            // THÊM: Khóa ngoại
            $table->foreign('masp')->references('masp')->on('sanpham')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lo_hang');
    }
};
