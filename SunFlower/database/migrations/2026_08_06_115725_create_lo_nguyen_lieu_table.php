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
        Schema::create('lo_nguyen_lieu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_nguyen_lieu');
            $table->unsignedBigInteger('id_phieu_nhap');
            $table->unsignedBigInteger('chi_tiet_id_phieu_nhap')->nullable();
            
            $table->string('malo')->comment('Mã lô');
            $table->integer('soluong_bandau')->comment('Số lượng nhập ban đầu');
            $table->integer('soluong_hientai')->comment('Số lượng tồn kho còn lại của lô này');
            $table->decimal('dongia', 15, 2)->comment('Đơn giá nhập của lô');
            $table->date('hsd')->nullable()->comment('Hạn sử dụng');
            $table->enum('status', ['Còn hàng', 'Hết hàng', 'Đã hủy'])->default('Còn hàng');
            
            $table->timestamps();

            $table->foreign('id_nguyen_lieu')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('id_phieu_nhap')->references('id')->on('phieu_nhap_kho')->onDelete('cascade');
            $table->foreign('chi_tiet_id_phieu_nhap')->references('id')->on('chi_tiet_phieu_nhap')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lo_nguyen_lieu');
    }
};
