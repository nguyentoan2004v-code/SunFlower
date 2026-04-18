<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SanPhamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Giữ nguyên tên Key để không làm hỏng Frontend & Web Controllers
            'masp' => $this->masp, 
            'tensp' => $this->tensp,
            'giaban' => (int) $this->giaban, 
            'giakm' => $this->giakm ? (int) $this->giakm : null,
            'hinhanh' => $this->hinhanh, // QUAN TRỌNG: Phải có ảnh
            'mota' => $this->mota,
            'madm' => $this->madm,
            
            // Trả về thêm tên danh mục (nếu có eager loading bằng with('danhmuc'))
            'danhmuc' => $this->whenLoaded('danhmuc', function () {
                return [
                    'madm' => $this->danhmuc->madm,
                    'tendm' => $this->danhmuc->tendm,
                ];
            }),
        ];
    }
}