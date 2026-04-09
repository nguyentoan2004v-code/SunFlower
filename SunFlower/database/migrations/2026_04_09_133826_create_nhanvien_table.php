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
        Schema::create('nhanvien', function (Blueprint $table) {
           // Cột manv: Khóa chính
            $table->char('manv', 10)->primary();
            
            // Cột hoten: Bắt buộc
            $table->string('hoten', 40);
            
            // Cột ngaysinh: Bắt buộc (Backend sẽ xử lý logic tuổi >= 18 sau)
            $table->date('ngaysinh');
            
            // Cột sdt: Bắt buộc, duy nhất
            $table->char('sdt', 15)->unique();
            
            // Cột luong: Bắt buộc, lớn hơn 0
            $table->decimal('luong', 12, 2);
            
            // Cột maquanly: Tham chiếu đến nhân viên khác. 
            // BẮT BUỘC phải cho phép rỗng (nullable) vì người Quản lý cao nhất sẽ không có ai quản lý.
            $table->char('maquanly', 10)->nullable();
            
            // Khóa ngoại tự tham chiếu bảng nhanvien
            $table->foreign('maquanly')->references('manv')->on('nhanvien')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nhanvien');
    }
};
