<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm 3 cột mới: giá nhập, nhà cung cấp, ghi chú
     * Tất cả nullable để tương thích ngược với dữ liệu cũ.
     */
    public function up(): void
    {
        Schema::table('lo_hang', function (Blueprint $table) {
            $table->decimal('gia_nhap', 12, 0)->nullable()->after('soluong_ton');
            $table->string('nhacungcap', 255)->nullable()->after('ngayhethan');
            $table->text('ghichu')->nullable()->after('nhacungcap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lo_hang', function (Blueprint $table) {
            $table->dropColumn(['gia_nhap', 'nhacungcap', 'ghichu']);
        });
    }
};
