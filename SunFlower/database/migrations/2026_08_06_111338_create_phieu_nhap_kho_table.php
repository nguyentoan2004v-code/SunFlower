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
        Schema::create('phieu_nhap_kho', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->comment('Mã phiếu nhập');
            $table->unsignedBigInteger('id_nhacungcap')->nullable()->comment('Nhà cung cấp');
            $table->string('manv', 10)->nullable()->comment('Nhân viên lập phiếu');
            $table->decimal('tongtien', 15, 2)->default(0)->comment('Tổng tiền đơn hàng');
            $table->text('note')->nullable()->comment('Ghi chú');
            $table->enum('status', ['Nháp', 'Hoàn thành', 'Đã hủy'])->default('Nháp')->comment('Trạng thái');
            
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_nhacungcap')->references('id')->on('nha_cung_cap')->onDelete('set null');
            $table->foreign('manv')->references('manv')->on('nhanvien')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phieu_nhap_kho');
    }
};
