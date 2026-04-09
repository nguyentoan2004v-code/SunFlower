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
        Schema::create('donhang', function (Blueprint $table) {
            // Khóa chính madon giới hạn 10 ký tự [cite: 260]
            $table->char('madon', 10)->primary(); 
            
            // Kiểu DateTime lưu cả ngày và giờ khách đặt [cite: 273]
            $table->dateTime('ngaydat'); 
            
            // Tổng tiền lớn hơn hoặc bằng 0 [cite: 274]
            $table->decimal('tongtien', 12, 2); 
            
            // Tình trạng đơn hàng (Chờ duyệt, Đang giao, Đã hủy...) [cite: 260]
            $table->string('trangthai', 20); 
            
            // Khóa ngoại liên kết với bảng Khách Hàng [cite: 260]
            $table->char('makh', 10);
            $table->foreign('makh')->references('makh')->on('khachhang')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donhang');
    }
};
