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
        Schema::create('chi_tiet_phieu_nhap', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_phieu_nhap');
            $table->unsignedBigInteger('id_nguyen_lieu');
            $table->integer('quantity')->comment('Số lượng nhập');
            $table->decimal('dongia', 15, 2)->comment('Đơn giá nhập');
            $table->decimal('subtotal', 15, 2)->comment('Thành tiền');
            $table->string('malo')->nullable()->comment('Mã Lô');
            $table->date('hsd')->nullable()->comment('Hạn sử dụng');
            
            $table->timestamps();

            $table->foreign('id_phieu_nhap')->references('id')->on('phieu_nhap_kho')->onDelete('cascade');
            $table->foreign('id_nguyen_lieu')->references('id')->on('materials')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_phieu_nhap');
    }
};
