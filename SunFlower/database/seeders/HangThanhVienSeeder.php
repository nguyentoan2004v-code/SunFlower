<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HangThanhVienSeeder extends Seeder
{
    public function run(): void
    {
        // Xóa dữ liệu cũ (nếu có) để tránh bị trùng lặp khi chạy lệnh nhiều lần
        DB::table('hang_thanh_vien')->truncate();

        $now = Carbon::now();

        DB::table('hang_thanh_vien')->insert([
            [
                'ten_hang' => 'Hạng Đồng',
                'chi_tieu_toi_thieu' => 0,
                'phan_tram_giam' => 0,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'ten_hang' => 'Hạng Bạc',
                'chi_tieu_toi_thieu' => 5000000,
                'phan_tram_giam' => 5,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'ten_hang' => 'Hạng Vàng',
                'chi_tieu_toi_thieu' => 10000000,
                'phan_tram_giam' => 10,
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'ten_hang' => 'Hạng Kim Cương',
                'chi_tieu_toi_thieu' => 50000000,
                'phan_tram_giam' => 30,
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}