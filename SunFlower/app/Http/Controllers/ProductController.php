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

    // Hàm trả về file hình ảnh theo mã sản phẩm
    public function showImage($masp)
    {
        $product = SanPham::where('masp', $masp)->firstOrFail();
        
        // Kiểm tra nếu sản phẩm chưa có ảnh (trường hinhanh bị null)
        if (empty($product->hinhanh)) {
            $defaultImage = public_path('images/bg-sunflower.jpg');
            if (file_exists($defaultImage)) {
                return response()->file($defaultImage);
            }
            abort(404);
        }
        
        // Nếu là link web (http) thì chuyển hướng thẳng tới link đó
        if (str_starts_with($product->hinhanh, 'http')) {
            return redirect($product->hinhanh);
        }
        
        // Nếu là file vật lý trong storage (Thêm folder image/ theo cấu trúc lưu trữ)
        $path = storage_path('app/public/' . ltrim($product->hinhanh, '/'));
        
        // Nếu file không tồn tại thật trong ổ cứng, trả về ảnh mặc định
        if (!file_exists($path) || is_dir($path)) {
            $defaultImage = public_path('images/bg-sunflower.jpg');
            if (file_exists($defaultImage)) {
                return response()->file($defaultImage);
            }
            abort(404);
        }
        
        return response()->file($path);
    }
}