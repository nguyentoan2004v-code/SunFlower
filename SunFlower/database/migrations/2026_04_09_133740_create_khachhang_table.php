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
        Schema::create('khachhang', function (Blueprint $table) {
            // Cột makh: Khóa chính, giới hạn 10 ký tự [cite: 257, 261]
            $table->char('makh', 10)->primary();
            
            // Cột hoten: Bắt buộc, giới hạn 40 ký tự [cite: 257]
            $table->string('hoten', 40);
            
            // Cột ngaysinh: Kiểu ngày tháng, cho phép rỗng (dựa theo bảng mô tả không tick chọn M - Mandatory) [cite: 257]
            $table->date('ngaysinh')->nullable();
            
            // Cột sdt: Bắt buộc, giới hạn 15 ký tự và phải là duy nhất (Unique) 
            $table->char('sdt', 15)->unique();
            
            // Cột diachi: Bắt buộc, giới hạn 100 ký tự [cite: 257]
            $table->string('diachi', 100);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khachhang');
    }
};
