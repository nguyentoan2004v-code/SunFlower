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
        Schema::create('vaitro', function (Blueprint $table) {
           $table->string('mavt', 10)->primary(); // VD: VT00000001
            $table->string('tenvt', 100);          // VD: Quản lý, Nhân viên bán hàng
            $table->string('mota', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaitro');
    }
};
