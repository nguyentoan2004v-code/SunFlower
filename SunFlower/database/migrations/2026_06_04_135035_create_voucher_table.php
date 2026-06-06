<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->string('mavoucher', 20)->primary(); // Khóa chính (Ví dụ: VALENTIN2026)
            $table->string('tenvoucher', 100);
            $table->string('loai_giam', 20); // 'phan_tram' hoặc 'so_tien'
            $table->decimal('gia_tri_giam', 12, 2);
            $table->decimal('giam_max', 12, 2)->nullable(); // Số tiền giảm tối đa nếu chọn loại %
            $table->decimal('don_min', 12, 2)->default(0); // Đơn hàng tối thiểu để được áp dụng
            $table->integer('soluong')->default(0);
            $table->integer('da_sudung')->default(0);
            $table->string('loai_ap_dung', 20); // 'tat_ca' hoặc 'danh_muc'
            $table->string('hien_thi', 20)->default('cong_khai'); // 'cong_khai' hoặc 'nhap_code'
            $table->dateTime('ngay_bd');
            $table->dateTime('ngay_kt');
            $table->tinyInteger('trangthai')->default(1); // 1: Kích hoạt, 0: Khóa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};