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
        Schema::create('chitiet_hoadon', function (Blueprint $table) {
            $table->char('mahd', 10);
            $table->char('masp', 10);
            $table->integer('soluong');
            $table->decimal('dongia', 12, 2); // Đổi integer thành decimal

            $table->primary(['mahd', 'masp']); // Thêm khóa chính kép

            $table->foreign('mahd')->references('mahd')->on('hoadon')->onDelete('cascade');
            $table->foreign('masp')->references('masp')->on('sanpham')->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chitiet_hoadon');
    }
};
