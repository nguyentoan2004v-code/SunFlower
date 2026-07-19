<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nha_cung_cap', function (Blueprint $table) {
            $table->id();
            $table->string('ten_ncc', 255)->unique();
            $table->timestamps();
        });

        // Seed sẵn một số nhà cung cấp quen thuộc
        DB::table('nha_cung_cap')->insert([
            ['ten_ncc' => 'Vườn hoa Đà Lạt', 'created_at' => now(), 'updated_at' => now()],
            ['ten_ncc' => 'Chợ hoa Hồ Thị Kỷ', 'created_at' => now(), 'updated_at' => now()],
            ['ten_ncc' => 'Trang trại hoa Sa Đéc', 'created_at' => now(), 'updated_at' => now()],
            ['ten_ncc' => 'Làng hoa Mê Linh', 'created_at' => now(), 'updated_at' => now()],
            ['ten_ncc' => 'Chợ đầu mối Đầm Sen', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('nha_cung_cap');
    }
};
