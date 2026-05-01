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
        Schema::table('hoadon', function (Blueprint $table) {
            $table->string('ten_cong_ty')->nullable()->after('ptthanhtoan');
            $table->string('mst', 20)->nullable()->after('ten_cong_ty');
            $table->string('dia_chi_cty')->nullable()->after('mst');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hoadon', function (Blueprint $table) {
            //
        });
    }
};
