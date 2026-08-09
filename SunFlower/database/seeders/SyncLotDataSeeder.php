<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NguyenLieu;
use App\Models\PhieuNhapKho;
use App\Models\ChiTietPhieuNhap;
use App\Models\LoNguyenLieu;
use App\Models\NhanVien;
use App\Models\NhaCungCap;
use App\Models\LichSuKho;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SyncLotDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            // Lấy tất cả nguyên liệu đang có tồn kho > 0 nhưng chưa có lô nào
            $materials = NguyenLieu::where('physical_stock', '>', 0)->get();

            if ($materials->isEmpty()) {
                $this->command->info("Không có nguyên liệu nào cần đồng bộ lô.");
                return;
            }

            // Lấy 1 nhân viên bất kỳ (hoặc nhân viên mặc định)
            $nhanVien = NhanVien::first();
            $manv = $nhanVien ? $nhanVien->manv : null;

            // Lấy 1 nhà cung cấp mặc định
            $nhaCungCap = NhaCungCap::first();
            $id_nhacungcap = $nhaCungCap ? $nhaCungCap->id : null;

            // Tạo 1 Phiếu Nhập Kho đặc biệt cho việc đồng bộ
            $phieuNhap = PhieuNhapKho::create([
                'code' => 'PN-SYNC-' . date('YmdHis'),
                'id_nhacungcap' => $id_nhacungcap,
                'manv' => $manv,
                'note' => 'Phiếu tự động tạo để đồng bộ dữ liệu Tồn Kho cũ sang Lô Nguyên Liệu',
                'status' => 'Hoàn thành', // Đã hoàn thành luôn
                'tongtien' => 0
            ]);

            $totalAmount = 0;

            foreach ($materials as $material) {
                // Kiểm tra xem đã có lô nào chưa
                $hasLot = LoNguyenLieu::where('id_nguyen_lieu', $material->id)->exists();
                if ($hasLot) {
                    continue; // Bỏ qua nếu đã có lô (tránh đồng bộ trùng)
                }

                $qty = $material->tonkho_thucte;
                $price = $material->gia_von ?? 0;
                $subtotal = $qty * $price;
                $totalAmount += $subtotal;

                // 1. Tạo Chi Tiết Phiếu Nhập
                $detail = ChiTietPhieuNhap::create([
                    'id_phieu_nhap' => $phieuNhap->id,
                    'id_nguyen_lieu' => $material->id,
                    'quantity' => $qty,
                    'dongia' => $price,
                    'subtotal' => $subtotal,
                    'malo' => 'LOT-SYNC-' . $material->id . '-' . date('ymd'),
                    'hsd' => Carbon::now()->addDays(30)->toDateString(), // Mặc định HSD 30 ngày
                ]);

                // 2. Tạo Lô Nguyên Liệu
                LoNguyenLieu::create([
                    'id_nguyen_lieu' => $material->id,
                    'id_phieu_nhap' => $phieuNhap->id,
                    'chi_tiet_id_phieu_nhap' => $detail->id,
                    'malo' => $detail->malo,
                    'soluong_bandau' => $qty,
                    'soluong_hientai' => $qty,
                    'dongia' => $price,
                    'hsd' => $detail->hsd,
                    'status' => 'Còn hàng',
                ]);

                // 3. Ghi Log
                LichSuKho::create([
                    'id_nguyen_lieu' => $material->id,
                    'type' => 'import',
                    'quantity' => $qty,
                    'note' => 'Đồng bộ tồn kho cũ thành Lô mới',
                    'manv' => $manv,
                ]);
            }

            // Cập nhật lại tổng tiền cho phiếu nhập
            $phieuNhap->update(['tongtien' => $totalAmount]);

            DB::commit();
            $this->command->info("Đã đồng bộ Lô thành công cho " . $materials->count() . " nguyên liệu.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Lỗi đồng bộ: " . $e->getMessage());
        }
    }
}
