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
        Schema::create('lichlamviec', function (Blueprint $table) {
            $table->char('maca', 10)->primary();
            $table->date('ngaynghi')->nullable(); // Có thể rỗng nếu ngày đó ca làm việc hoạt động bình thường
            $table->time('giolam');
            $table->time('giotan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lichlamviec');
    }
};
