<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            // Thêm cột liên kết mã voucher và số tiền được giảm vào sau cột makh
            $table->string('mavoucher', 20)->nullable()->after('makh');
            $table->decimal('tiengiam', 12, 2)->default(0)->after('mavoucher');
            
            // Định nghĩa khóa ngoại kết nối sang bảng voucher vừa tạo ở trên
            $table->foreign('mavoucher')->references('mavoucher')->on('voucher')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropForeign(['mavoucher']);
            $table->dropColumn(['mavoucher', 'tiengiam']);
        });
    }
};