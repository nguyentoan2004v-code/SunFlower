<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NguyenLieu;
use App\Models\SanPham;
use App\Models\LichSuKho;
use Illuminate\Support\Facades\DB;

class MaterialMigrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ (nếu có)
        DB::table('product_material')->truncate();
        DB::table('inventory_logs')->truncate();
        DB::table('materials')->truncate();
        
        $this->command->info('Đang tạo dữ liệu nguyên liệu mẫu...');

        // 1. Tạo một số nguyên liệu cơ bản
        $materialsData = [
            ['name' => 'Hoa hồng đỏ (Đà Lạt)', 'unit' => 'cành', 'cost_price' => 5000, 'physical_stock' => 500],
            ['name' => 'Hoa hồng trắng (Đà Lạt)', 'unit' => 'cành', 'cost_price' => 5500, 'physical_stock' => 300],
            ['name' => 'Hoa hướng dương', 'unit' => 'cành', 'cost_price' => 12000, 'physical_stock' => 150],
            ['name' => 'Hoa cẩm chướng', 'unit' => 'cành', 'cost_price' => 6000, 'physical_stock' => 200],
            ['name' => 'Hoa baby trắng', 'unit' => 'bó', 'cost_price' => 35000, 'physical_stock' => 50],
            ['name' => 'Lá bạc (Moka)', 'unit' => 'bó', 'cost_price' => 25000, 'physical_stock' => 100],
            ['name' => 'Xốp cắm hoa Oasis', 'unit' => 'cái', 'cost_price' => 15000, 'physical_stock' => 500],
            ['name' => 'Giấy gói Kraft', 'unit' => 'tờ', 'cost_price' => 2000, 'physical_stock' => 1000],
            ['name' => 'Giấy gói lưới', 'unit' => 'tờ', 'cost_price' => 3500, 'physical_stock' => 800],
            ['name' => 'Ruy băng lụa đỏ', 'unit' => 'mét', 'cost_price' => 1000, 'physical_stock' => 500],
            ['name' => 'Ruy băng voan trắng', 'unit' => 'mét', 'cost_price' => 1200, 'physical_stock' => 500],
            ['name' => 'Lẵng mây đan', 'unit' => 'cái', 'cost_price' => 45000, 'physical_stock' => 100],
            ['name' => 'Hộp giấy kính', 'unit' => 'cái', 'cost_price' => 35000, 'physical_stock' => 100],
        ];

        $materials = [];
        foreach ($materialsData as $data) {
            $m = NguyenLieu::create([
                'name'           => $data['name'],
                'unit'           => $data['unit'],
                'cost_price'     => $data['cost_price'],
                'physical_stock' => $data['physical_stock'],
                'reserved_stock' => 0,
            ]);
            $materials[$m->id] = $m;

            // Ghi log nhập kho khởi tạo
            LichSuKho::create([
                'id_nguyen_lieu' => $m->id,
                'type'        => 'import',
                'quantity'    => $data['physical_stock'],
                'note'        => 'Nhập kho khởi tạo hệ thống BOM',
                'manv'        => null, // Hệ thống tự động
            ]);
        }

        $this->command->info('Đã tạo ' . count($materials) . ' nguyên liệu.');
        $this->command->info('Đang gắn BOM mẫu cho các sản phẩm...');

        // 2. Lấy tất cả sản phẩm và tạo BOM ngẫu nhiên hợp lý
        $sanPhams = SanPham::all();
        $count = 0;

        foreach ($sanPhams as $sp) {
            $name = strtolower($sp->tensp);
            $bom = [];

            // Logic gắn BOM cơ bản dựa theo tên sản phẩm
            if (str_contains($name, 'hồng đỏ')) {
                $bom[1] = ['quantity' => rand(5, 20)]; // Hoa hồng đỏ
                $bom[5] = ['quantity' => rand(1, 2)];  // Baby
            } elseif (str_contains($name, 'hướng dương')) {
                $bom[3] = ['quantity' => rand(3, 10)]; // Hướng dương
                $bom[6] = ['quantity' => rand(1, 3)];  // Lá bạc
            } else {
                // Mặc định random 2-3 loại hoa
                $randomFlowers = array_rand([1,2,3,4], rand(1, 2));
                if (!is_array($randomFlowers)) $randomFlowers = [$randomFlowers];
                foreach ($randomFlowers as $idx) {
                    $bom[array_keys([1=>1, 2=>2, 3=>3, 4=>4])[$idx]] = ['quantity' => rand(5, 15)];
                }
            }

            // Thêm phụ liệu
            if (str_contains($name, 'lẵng') || str_contains($name, 'giỏ')) {
                $bom[12] = ['quantity' => 1]; // Lẵng mây
                $bom[7] = ['quantity' => rand(1, 2)]; // Xốp
            } elseif (str_contains($name, 'hộp')) {
                $bom[13] = ['quantity' => 1]; // Hộp
                $bom[7] = ['quantity' => 1];  // Xốp
            } else {
                // Bó hoa
                $bom[8] = ['quantity' => rand(2, 5)]; // Giấy kraft
                $bom[10] = ['quantity' => rand(1, 3)]; // Ruy băng đỏ
            }

            // Sync BOM vào DB
            $sp->nguyenLieus()->sync($bom);
            $count++;
        }

        $this->command->info("Đã gắn BOM cho $count sản phẩm thành công!");
    }
}
