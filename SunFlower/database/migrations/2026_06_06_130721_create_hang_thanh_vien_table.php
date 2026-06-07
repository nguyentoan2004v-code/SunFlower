<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hang_thanh_vien', function (Blueprint $table) {
            $table->id();
            $table->string('ten_hang'); // Đồng, Bạc, Vàng, Kim Cương
            $table->integer('chi_tieu_toi_thieu')->default(0); // Mốc tiền
            $table->integer('phan_tram_giam')->default(0); // % giảm giá
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hang_thanh_vien');
    }
};
