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
        Schema::create('chitiet_hoadon', function (Blueprint $table) {
           $table->string('mahd', 10);
            $table->string('masp', 10);
            $table->integer('soluong');
            $table->integer('dongia'); // Lưu cứng đơn giá lúc xuất hóa đơn
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitiet_hoadon');
    }
};
