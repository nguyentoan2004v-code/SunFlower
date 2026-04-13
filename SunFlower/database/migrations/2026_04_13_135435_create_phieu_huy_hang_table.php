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
        Schema::create('phieu_huy_hang', function (Blueprint $table) {
           $table->string('maphieu', 10)->primary();
            $table->string('malo', 10);
            $table->string('masp', 10);
            $table->integer('soluong_huy');
            $table->date('ngayhuy');
            $table->string('lydo', 255); // VD: Hoa héo, dập nát do vận chuyển
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_huy_hang');
    }
};
