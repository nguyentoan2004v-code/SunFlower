<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm index để tăng tốc query Dashboard và báo cáo.
     */
    public function up(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            // Index đơn — tăng tốc WHERE trangthai = ? và ORDER BY ngaydat
            $table->index('trangthai', 'idx_donhang_trangthai');
            $table->index('ngaydat',   'idx_donhang_ngaydat');

            // Composite index — makh đứng trước vì cardinality cao hơn trangthai
            // (makh có nhiều giá trị khác nhau hơn → MySQL lọc được nhiều hơn ở bước đầu)
            // Dùng cho: WHERE makh = ? AND trangthai = ? (trang lịch sử đơn hàng)
            $table->index(['makh', 'trangthai'], 'idx_donhang_makh_trangthai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donhang', function (Blueprint $table) {
            $table->dropIndex('idx_donhang_trangthai');
            $table->dropIndex('idx_donhang_ngaydat');
            $table->dropIndex('idx_donhang_makh_trangthai');
        });
    }
};
