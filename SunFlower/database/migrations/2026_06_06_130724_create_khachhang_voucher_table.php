<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('khachhang_voucher', function (Blueprint $table) {
            $table->id();
            $table->string('makh', 50); // Khớp với khóa chính của bảng khachhang
            $table->string('mavoucher', 20); // Khớp với khóa chính của bảng voucher
            $table->tinyInteger('trang_thai')->default(0); 
            $table->timestamp('ngay_doi')->useCurrent();
            $table->timestamps();

            // Sửa lại tên cột tham chiếu cho đúng
            $table->foreign('makh')->references('makh')->on('khachhang')->onDelete('cascade');
            $table->foreign('mavoucher')->references('mavoucher')->on('voucher')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khachhang_voucher');
    }
};
