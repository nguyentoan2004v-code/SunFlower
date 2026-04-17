<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    /**
     * [API index] Lấy danh sách hoa, hỗ trợ Tìm kiếm & Lọc theo giá/danh mục
     * Dùng cho trang danh sách sản phẩm chính.
     */
    public function index(Request $request)
    {
        // Khởi tạo query kèm thông tin danh mục
        $query = SanPham::with('danhmuc');

        // Lọc theo từ khóa (nếu dùng param ?search=...)
        if ($request->has('search')) {
            $tuKhoa = $request->input('search');
            $query->where('tensp', 'LIKE', '%' . $tuKhoa . '%');
        }

        // Lọc theo mã danh mục (?madm=...)
        if ($request->has('madm')) {
            $query->where('madm', $request->input('madm'));
        }

        // Lọc theo khoảng giá (?min_price=...&max_price=...)
        if ($request->has('min_price')) {
            $query->where('giaban', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('giaban', '<=', $request->input('max_price'));
        }

        $sanphams = $query->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Lấy danh sách hoa thành công!',
            'total' => $sanphams->count(),
            'data' => $sanphams
        ], 200);
    }

    /**
     * [API show] Lấy chi tiết một đóa hoa theo mã sản phẩm (masp)
     */
    public function show($masp)
    {
        /** * Lưu ý: Tui đã bỏ 'lichsugias' vì bảng này chưa có trong file SQL của bro.
         * Khi nào bro tạo bảng đó thì hãy thêm lại vào with().
         */
        $sanpham = SanPham::with(['danhmuc'])->find($masp);

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

    /**
     * [API search] Chuyên dụng cho thanh tìm kiếm trên Header
     * Nhận biến ?query=... từ Frontend.
     */
    public function search(Request $request) 
{
    // Loại bỏ khoảng trắng thừa
    $keyword = trim($request->query('query')); 
    
    if (empty($keyword)) {
        return response()->json(['status' => 'success', 'data' => []]);
    }

    // Tìm kiếm KHÔNG phân biệt hoa thường (LOWER)
    $sanphams = \App\Models\SanPham::whereRaw('LOWER(tensp) LIKE ?', ['%' . strtolower($keyword) . '%'])->get();

    return response()->json([
        'status' => 'success',
        'data' => $sanphams
    ]);
}
}