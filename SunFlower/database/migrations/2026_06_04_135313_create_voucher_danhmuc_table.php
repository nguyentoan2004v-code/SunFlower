<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_danhmuc', function (Blueprint $table) {
            $table->string('mavoucher', 20);
            $table->char('madm', 10); // Đồng bộ độ dài char(10) với bảng danhmuc của bạn
            
            $table->primary(['mavoucher', 'madm']);
            
            // Thiết lập khóa ngoại bảo mật dữ liệu
            $table->foreign('mavoucher')->references('mavoucher')->on('voucher')->onDelete('cascade');
            $table->foreign('madm')->references('madm')->on('danhmuc')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_danhmuc');
    }
};