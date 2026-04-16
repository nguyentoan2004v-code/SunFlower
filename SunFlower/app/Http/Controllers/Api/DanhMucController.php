<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    /**
     * [Giai đoạn 1.3] API Lấy danh sách Danh mục
     * Phương thức: GET
     */
    public function index()
    {
        // Tuyệt chiêu withCount: Tự động đếm xem mỗi danh mục có bao nhiêu sản phẩm
        // Laravel sẽ tự động tạo ra một cột ảo tên là 'sanphams_count'
        $danhmucs = DanhMuc::withCount('sanphams')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh mục thành công!',
            'data' => $danhmucs
        ], 200);
    }
}