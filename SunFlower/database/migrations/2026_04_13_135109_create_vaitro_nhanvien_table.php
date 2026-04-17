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
        Schema::create('vaitro_nhanvien', function (Blueprint $table) {
          // SỬA string THÀNH char ĐỂ KHỚP VỚI BẢNG GỐC
            $table->char('manv', 10);
            $table->char('mavt', 10); // Lưu ý: bảng vaitro bạn đang để mavt là string, bạn nên sửa bảng vaitro thành char('mavt', 10) luôn nhé.

            // THÊM: Khóa chính kép
            $table->primary(['manv', 'mavt']);

            // THÊM: Khóa ngoại
            $table->foreign('manv')->references('manv')->on('nhanvien')->onDelete('cascade');
            $table->foreign('mavt')->references('mavt')->on('vaitro')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaitro_nhanvien');
    }
};
