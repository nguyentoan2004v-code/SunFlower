<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LoHang;
use App\Models\PhieuHuyHang;
use App\Models\NhanVien;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoCancelExpiredFlowers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-cancel-expired-flowers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động hủy các lô hoa đã hết hạn sử dụng nhưng còn tồn kho.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        $expiredLohangs = LoHang::whereDate('ngayhethan', '<=', $today)
            ->where('soluong_ton', '>', 0)
            ->get();

        if ($expiredLohangs->isEmpty()) {
            $this->info('Không có lô hàng hết hạn nào cần hủy hôm nay.');
            return;
        }

        // Tìm một nhân viên quản lý để gán vào phiếu hủy tự động
        $admin = NhanVien::whereHas('vaitros', function ($q) {
            $q->whereIn('ten_vaitro', ['Quản lý Cửa hàng', 'Quản lý Kho hàng']);
        })->first();

        // Nếu không có admin nào, lấy đại nhân viên đầu tiên
        if (!$admin) {
            $admin = NhanVien::first();
        }

        if (!$admin) {
            $this->error('Không tìm thấy nhân viên nào trong hệ thống để gán người lập phiếu hủy.');
            return;
        }

        $count = 0;

        foreach ($expiredLohangs as $loHang) {
            DB::beginTransaction();
            try {
                // Lock lô hàng để đảm bảo không bị race condition
                $lockedLoHang = LoHang::where('malo', $loHang->malo)->lockForUpdate()->first();
                
                if ($lockedLoHang && $lockedLoHang->soluong_ton > 0) {
                    $soluongHuy = $lockedLoHang->soluong_ton;
                    
                    // Sinh mã phiếu hủy tự động
                    $lastPhieu = PhieuHuyHang::lockForUpdate()->orderBy('maphieu', 'desc')->first();
                    $newMaPhieu = $lastPhieu 
                        ? 'PH' . str_pad(intval(substr($lastPhieu->maphieu, 2)) + 1, 8, '0', STR_PAD_LEFT) 
                        : 'PH00000001';

                    $phieuHuy = new PhieuHuyHang();
                    $phieuHuy->maphieu = $newMaPhieu;
                    $phieuHuy->malo = $lockedLoHang->malo;
                    $phieuHuy->masp = $lockedLoHang->masp;
                    $phieuHuy->manv = $admin->manv;
                    $phieuHuy->soluong_huy = $soluongHuy;
                    $phieuHuy->ngayhuy = $today->toDateString();
                    $phieuHuy->lydo = 'Tự động hủy do hết hạn sử dụng';
                    $phieuHuy->save();

                    // Cập nhật tồn kho về 0
                    $lockedLoHang->soluong_ton = 0;
                    $lockedLoHang->save();

                    DB::commit();
                    $count++;
                    $this->info("Đã hủy tự động lô {$lockedLoHang->malo} (Số lượng: {$soluongHuy}).");
                } else {
                    DB::rollBack();
                }

            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Lỗi khi hủy lô {$loHang->malo}: " . $e->getMessage());
            }
        }

        $this->info("Hoàn tất! Đã tự động lập $count phiếu hủy hàng.");
    }
}
