<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// 1. Import các Model cần thiết thay vì dùng ApiCaller
use App\Models\DanhMuc;
use App\Models\SanPham;

class HomeController extends Controller {
    
    // Đã xóa trait ApiCaller và hàm extractData()

    public function index() {
        // Lấy dữ liệu trực tiếp từ CSDL
        $categories = DanhMuc::all();
        $allProducts = SanPham::all(); 

        // Lấy 8 sản phẩm đầu tiên (Collection)
        $products = collect($allProducts)->take(8);
        
        // Random ảnh hero một cách an toàn
        $heroImage = null;
        if ($products->isNotEmpty()) {
            $heroImage = route('product.image', $products->random()->masp);
        }

        return view('home', compact('categories', 'products', 'heroImage'));
    }

    // 1. Trang Tất cả danh mục / sản phẩm
    public function allCategories() {
        // Truy vấn thẳng CSDL
        $categories = DanhMuc::all();
        $products = SanPham::all();
        
        return view('categories.index', compact('categories', 'products'));
    }

    // 2. Trang Chi tiết 1 Danh mục
    public function categoryDetail($madm) {
        $categories = DanhMuc::all();
        
        $categoryProducts = \App\Models\SanPham::where('madm', $madm)->get();
        
        return view('category.show', compact('categoryProducts', 'categories'));
    }

    // 3. Trang Chi tiết 1 Sản phẩm
    public function productDetail($masp) {
        // Lấy 1 sản phẩm kèm luôn thông tin danh mục của nó
        $product = SanPham::with('danhmuc')->find($masp);
        
        // Bắt lỗi nếu người dùng gõ sai mã sản phẩm trên URL
        if (!$product) {
            abort(404, 'Sản phẩm không tồn tại!'); 
        }

        return view('product.show', compact('product'));
    }

    // Xử lý luồng Tìm kiếm
    public function search(Request $request) {
        $keyword = trim($request->query('query'));

        if (empty($keyword)) {
            return redirect()->route('home');
        }

        // Tái sử dụng logic tìm kiếm trực tiếp bằng Eloquent (không gọi qua cổng 8000 nữa)
        $products = SanPham::whereRaw('LOWER(tensp) LIKE ?', ['%' . strtolower($keyword) . '%'])->get();

        return view('search.results', compact('products', 'keyword'));
    }

    // Hàm trả về file hình ảnh theo mã danh mục
    public function showCategoryImage($madm)
    {
        $category = DanhMuc::where('madm', $madm)->firstOrFail();
        
        // Kiểm tra sớm nếu null hoặc rỗng để tránh lỗi TypeError ở các hàm xử lý chuỗi
        if (empty($category->hinhanh)) {
            return redirect('https://images.unsplash.com/photo-1563241527-3004b7be0ffd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60');
        }
        
        if (str_starts_with($category->hinhanh, 'http')) {
            return redirect($category->hinhanh);
        }
        
        $path = storage_path('app/public/image/' . ltrim($category->hinhanh, '/'));
        
        if (!file_exists($path) || is_dir($path)) {
            return redirect('https://images.unsplash.com/photo-1563241527-3004b7be0ffd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60');
        }
        
        return response()->file($path);
    }

    // Trang Giới thiệu
    public function about()
    {
        return view('about');
    }
}