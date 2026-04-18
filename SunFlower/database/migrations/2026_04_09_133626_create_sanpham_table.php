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
        Schema::create('sanpham', function (Blueprint $table) {
           // Cột masp: Kiểu chuỗi 10 ký tự, là Khóa chính, không rỗng
            $table->char('masp', 10)->primary();
            
            // Cột tensp: Chuỗi 50 ký tự, bắt buộc
            $table->string('tensp', 50);
            
            // Cột giaban: Kiểu số thập phân (tổng 12 chữ số, 2 số sau dấu phẩy), không rỗng
            $table->decimal('giaban', 12, 2);
            
            // Cột mota: Chuỗi 255 ký tự, bắt buộc
            $table->string('mota', 255);
            
            // Cột hinhanh: Đường dẫn ảnh sản phẩm
            $table->string('hinhanh', 255)->nullable();
            
            // Cột giakm: Kiểu số thập phân, cho phép rỗng (nullable) vì không phải lúc nào cũng có khuyến mãi
            $table->decimal('giakm', 12, 2)->nullable();
            
            // Cột madm: Khóa ngoại, phải có cùng kiểu dữ liệu với khóa chính của bảng danhmuc
            $table->char('madm', 10);
            
            // Khai báo khóa ngoại: móc cột madm vào cột madm của bảng danhmuc
            // onDelete('restrict'): Không cho phép xóa danh mục nếu bên trong vẫn còn chứa sản phẩm
            $table->foreign('madm')->references('madm')->on('danhmuc')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanpham');
    }
};
