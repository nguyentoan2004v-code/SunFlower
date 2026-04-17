<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SanPhamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ma_hoa' => $this->masp, // Đổi tên key cho FE dễ đọc
            'ten_hoa' => $this->tensp,
            'gia_ban' => (int) $this->giaban, // Ép kiểu về số nguyên cho chuẩn
            'gia_khuyen_mai' => $this->giakm ? (int) $this->giakm : null,
            'mo_ta' => $this->mota,
            // Ẩn created_at và updated_at đi vì FE không cần
        ];
    }
}