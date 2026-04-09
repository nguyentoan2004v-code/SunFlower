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
        Schema::create('danhmuc', function (Blueprint $table) {
           // Tạo cột madm: kiểu char giới hạn 10 ký tự và đánh dấu là Khóa chính
            $table->char('madm', 10)->primary(); 
            
            // Tạo cột tendm: kiểu chuỗi giới hạn 50 ký tự (mặc định là bắt buộc/không được rỗng)
            $table->string('tendm', 50); 
            
            // Dòng này Laravel tự sinh ra để theo dõi thời gian tạo và cập nhật dữ liệu (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danhmuc');
    }
};
