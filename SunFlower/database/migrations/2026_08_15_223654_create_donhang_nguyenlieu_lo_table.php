<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donhang_nguyenlieu_lo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_chitiet_donhang_nguyenlieu');
            $table->unsignedBigInteger('id_lo');
            $table->integer('soluong');
            $table->timestamps();

            $table->foreign('id_chitiet_donhang_nguyenlieu', 'fk_dhnl_lo')
                  ->references('id')
                  ->on('order_item_materials')
                  ->onDelete('cascade');
                  
            $table->foreign('id_lo')
                  ->references('id')
                  ->on('lo_nguyen_lieu')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donhang_nguyenlieu_lo');
    }
};
