<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
   /**
     * [Giai đoạn 1.1 + 1.4] API Lấy danh sách, Tìm kiếm & Lọc sản phẩm
     * Phương thức: GET
     */
    public function index(Request $request)
    {
        // 1. Khởi tạo câu truy vấn (chưa lấy dữ liệu ngay)
        $query = SanPham::with('danhmuc');

        // 2. Nếu Frontend có truyền lên chữ cần tìm (search)
        if ($request->has('search')) {
            $tuKhoa = $request->input('search');
            // Tìm các hoa có tên chứa từ khóa
            $query->where('tensp', 'LIKE', '%' . $tuKhoa . '%');
        }

        // 3. Nếu Frontend muốn lọc theo Danh mục (madm)
        if ($request->has('madm')) {
            $query->where('madm', $request->input('madm'));
        }

        // 4. Nếu Frontend lọc theo khoảng giá (min_price, max_price)
        if ($request->has('min_price')) {
            $query->where('giaban', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('giaban', '<=', $request->input('max_price'));
        }

        // 5. Sau khi đã ráp đủ các điều kiện, tiến hành lấy dữ liệu
        $sanphams = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách hoa thành công!',
            'total' => $sanphams->count(),
            'data' => $sanphams
        ], 200);
    }

    /**
     * [Giai đoạn 1.2] API Lấy chi tiết 1 sản phẩm theo Mã (masp)
     * Phương thức: GET
     */
    public function show($masp)
    {
        // Lấy sản phẩm theo mã, kèm Danh mục và Lịch sử giá (sắp xếp giá mới nhất lên đầu)
        $sanpham = SanPham::with(['danhmuc', 'lichsugias' => function($query) {
            $query->orderBy('ngay_ap_dung', 'desc');
        }])->find($masp);

        // Bắt lỗi nếu Frontend truyền sai mã hoa
        if (!$sanpham) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy sản phẩm này!'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy chi tiết sản phẩm thành công!',
            'data' => $sanpham
        ], 200);
    }
}