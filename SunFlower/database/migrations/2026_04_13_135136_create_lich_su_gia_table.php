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
        Schema::create('lich_su_gia', function (Blueprint $table) {
           $table->char('magia', 10)->primary(); 
            $table->char('masp', 10); // SỬA string THÀNH char

            $table->integer('giaban'); 
            $table->dateTime('ngay_ap_dung'); 

            // THÊM: Khóa ngoại
            $table->foreign('masp')->references('masp')->on('sanpham')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lich_su_gia');
    }
};
