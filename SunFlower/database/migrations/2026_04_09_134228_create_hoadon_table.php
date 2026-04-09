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
        Schema::create('hoadon', function (Blueprint $table) {
           $table->char('mahd', 10)->primary();
            
            // Tổng tiền tính theo công thức [cite: 259]
            $table->decimal('tongtien', 12, 2); 
            
            // Thuế [cite: 259]
            $table->decimal('thue', 12, 2); 
            
            // Ngày xuất hóa đơn [cite: 259]
            $table->date('ngayxuat'); 
            
            // Phương thức thanh toán (Tiền mặt, Chuyển khoản) [cite: 260]
            $table->string('ptthanhtoan', 20); 
            
            // Khóa ngoại liên kết với đơn hàng. 
            // Cực kỳ quan trọng: Thêm hàm unique() để đảm bảo quan hệ 1-1 
            $table->char('madon', 10)->unique(); 
            $table->foreign('madon')->references('madon')->on('donhang')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoadon');
    }
};
