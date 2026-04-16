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
        Schema::table('donhang', function (Blueprint $table) {
            $table->string('sdt_nhan', 15)->after('makh');   // Số điện thoại người nhận hoa
            $table->string('diachi_giao')->after('sdt_nhan'); // Địa chỉ giao hoa thực tế
            $table->text('ghichu')->nullable()->after('diachi_giao'); // Lời nhắn, ghi chú
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            //
        });
    }
};
