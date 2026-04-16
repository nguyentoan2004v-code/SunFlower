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
        Schema::create('chitiet_donhang', function (Blueprint $table) {
        $table->char('madon', 10); // Khóa ngoại liên kết với bảng donhang
        $table->char('masp', 10);  // Khóa ngoại liên kết với bảng sanpham
        
        $table->integer('soluong'); // Khách mua bao nhiêu bó?
        
        // GIÁ BÁN: Lưu lại giá tại lúc khách bấm nút mua
        $table->decimal('giaban', 12, 2); 

        // Khai báo khóa ngoại
        $table->foreign('madon')->references('madon')->on('donhang')->onDelete('cascade');
        $table->foreign('masp')->references('masp')->on('sanpham');

        // Khóa chính là sự kết hợp của Mã đơn và Mã sản phẩm
        $table->primary(['madon', 'masp']);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitiet_donhang');
    }
};
