<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoNguyenLieu extends Model
{
    use HasFactory;

    protected $table = 'lo_nguyen_lieu';

    protected $fillable = [
        'id_nguyen_lieu',
        'id_phieu_nhap',
        'id_chitiet_phieu_nhap',
        'malo',
        'soluong_bandau',
        'soluong_hientai',
        'dongia',
        'hsd',
        'trangthai',
    ];

    public function nguyenLieu()
    {
        return $this->belongsTo(NguyenLieu::class, 'id_nguyen_lieu');
    }

    public function phieuNhap()
    {
        return $this->belongsTo(PhieuNhapKho::class, 'id_phieu_nhap');
    }

    public function chiTietPhieuNhap()
    {
        return $this->belongsTo(ChiTietPhieuNhap::class, 'id_chitiet_phieu_nhap');
    }

    public function chiTietPhieuHuys()
    {
        return $this->hasMany(ChiTietPhieuHuy::class, 'id_lo_nguyen_lieu');
    }

    /**
     * Hàm tĩnh trừ số lượng tồn kho theo nguyên tắc FEFO
     * @param int $id_nguyen_lieu
     * @param int $qty_to_deduct Số lượng cần trừ
     * @return array Danh sách các lô đã bị trừ (để log lại nếu cần)
     */
    public static function deductStock($id_nguyen_lieu, $qty_to_deduct)
    {
        $remainingToDeduct = $qty_to_deduct;
        $deductedLots = [];

        $lots = self::where('id_nguyen_lieu', $id_nguyen_lieu)
                    ->where('soluong_hientai', '>', 0)
                    ->orderByRaw('ISNULL(hsd), hsd ASC')
                    ->orderBy('created_at', 'asc')
                    ->lockForUpdate()
                    ->get();

        foreach ($lots as $lot) {
            if ($remainingToDeduct <= 0) break;

            if ($lot->soluong_hientai >= $remainingToDeduct) {
                $lot->soluong_hientai -= $remainingToDeduct;
                if ($lot->soluong_hientai == 0) {
                    $lot->trangthai = 'Hết hàng';
                }
                $lot->save();

                $deductedLots[] = [
                    'id_lo' => $lot->id,
                    'malo' => $lot->malo,
                    'deducted_qty' => $remainingToDeduct
                ];
                $remainingToDeduct = 0;
            } else {
                $deductedQty = $lot->soluong_hientai;
                $remainingToDeduct -= $deductedQty;
                
                $lot->soluong_hientai = 0;
                $lot->trangthai = 'Hết hàng';
                $lot->save();

                $deductedLots[] = [
                    'id_lo' => $lot->id,
                    'malo' => $lot->malo,
                    'deducted_qty' => $deductedQty
                ];
            }
        }

        if ($remainingToDeduct > 0) {
            throw new \Exception("Không đủ tồn kho khả dụng để xuất (Thiếu {$remainingToDeduct} đơn vị).");
        }

        return $deductedLots;
    }
}
