<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;

class ProductController extends Controller
{
    public function show($masp)
    {
        // 1. Lấy chi tiết 1 sản phẩm dựa vào mã sản phẩm (masp)
        // Load thêm quan hệ 'danhMuc' để hiển thị tên danh mục trên giao diện
        $product = SanPham::with('danhMuc')->where('masp', $masp)->firstOrFail();

        // 2. Lấy thêm 4 sản phẩm liên quan
        // SỬA LỖI: Đổi 'madv' thành 'madm' để khớp với cột trong bảng sanpham của bạn
        $relatedProducts = SanPham::where('madm', $product->madm)
                                  ->where('masp', '!=', $masp)
                                  ->take(4)
                                  ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
    public function category($madm) {
    // Lấy thông tin danh mục hiện tại
    $category = \App\Models\DanhMuc::where('madm', $madm)->firstOrFail();
    
    // Lấy tất cả sản phẩm thuộc danh mục này
    $products = SanPham::where('madm', $madm)->get();
    
    // Trả về một view riêng để hiển thị danh sách đã lọc
    return view('products.category', compact('category', 'products'));
}
}