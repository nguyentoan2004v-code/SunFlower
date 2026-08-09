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
        Schema::create('chi_tiet_phieu_huy', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_phieu_huy');
            $table->unsignedBigInteger('id_lo_nguyen_lieu');
            $table->integer('so_luong_huy');
            $table->string('ly_do_chi_tiet', 255)->nullable();
            $table->string('hinh_anh_minh_chung', 255)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_phieu_huy')->references('id')->on('phieu_huy_hang')->onDelete('cascade');
            $table->foreign('id_lo_nguyen_lieu')->references('id')->on('lo_nguyen_lieu')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_huy');
    }
};
