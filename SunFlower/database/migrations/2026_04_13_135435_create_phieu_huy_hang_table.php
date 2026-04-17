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
        Schema::create('phieu_huy_hang', function (Blueprint $table) {
            $table->char('maphieu', 10)->primary(); // SỬA string THÀNH char
            $table->char('malo', 10); // SỬA string THÀNH char
            $table->char('masp', 10); // SỬA string THÀNH char

            $table->integer('soluong_huy');
            $table->date('ngayhuy');
            $table->string('lydo', 255); 

            // THÊM: Khóa ngoại
            $table->foreign('malo')->references('malo')->on('lo_hang')->onDelete('cascade');
            $table->foreign('masp')->references('masp')->on('sanpham')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_huy_hang');
    }
};
