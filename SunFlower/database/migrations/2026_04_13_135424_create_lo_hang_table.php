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
           $table->string('malo', 10)->primary();
            $table->string('masp', 10);
            $table->integer('soluong_nhap');
            $table->integer('soluong_ton');
            $table->date('ngaynhap');
            $table->date('ngayhethan'); // Ngày hoa dự kiến sẽ tàn
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
