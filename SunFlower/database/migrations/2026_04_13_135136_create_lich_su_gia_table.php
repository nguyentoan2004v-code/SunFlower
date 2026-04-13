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
           $table->string('magia', 10)->primary(); 
            $table->string('masp', 10);
            $table->integer('giaban'); // Giá bán tại thời điểm đó
            $table->dateTime('ngay_ap_dung'); // Ngày bắt đầu áp dụng giá này
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
