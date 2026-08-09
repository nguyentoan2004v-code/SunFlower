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
        // Xóa bảng con trước (phieu_huy_hang phụ thuộc lo_hang)
        Schema::dropIfExists('phieu_huy_hang');
        // Sau đó xóa bảng cha
        Schema::dropIfExists('lo_hang');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
