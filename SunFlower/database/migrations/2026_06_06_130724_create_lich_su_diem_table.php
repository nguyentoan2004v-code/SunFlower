<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lich_su_diem', function (Blueprint $table) {
            $table->id();
            $table->string('makh', 50); // Khớp với bảng khachhang
            $table->string('loai_giao_dich'); 
            $table->integer('so_diem');
            $table->string('mo_ta')->nullable(); 
            $table->timestamps();

            $table->foreign('makh')->references('makh')->on('khachhang')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lich_su_diem');
    }
};
