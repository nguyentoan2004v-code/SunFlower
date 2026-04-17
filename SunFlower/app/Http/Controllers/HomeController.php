<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Traits\ApiCaller;

class HomeController extends Controller {
    use ApiCaller;

    private function extractData($response) {
        return isset($response['data']) ? $response['data'] : (is_array($response) ? $response : []);
    }

    public function index() {
        // Gọi API công khai (Khớp với api/danhmucs và api/sanphams)
        $categories = $this->extractData($this->callApi('/api/danhmucs'));
        $allProducts = $this->extractData($this->callApi('/api/sanphams'));

        $products = collect($allProducts)->take(8)->toArray();
        $heroImage = !empty($products) ? asset('storage/' . collect($products)->random()['hinhanh']) : null;

        return view('home', compact('categories', 'products', 'heroImage'));
    }
    // 1. Trang Tất cả danh mục / sản phẩm
    public function allCategories() {
        $categories = $this->extractData($this->callApi('/api/danhmucs'));
        $products = $this->extractData($this->callApi('/api/sanphams'));
        
        // Cần tạo thêm file giao diện: resources/views/categories/index.blade.php
        return view('categories.index', compact('categories', 'products'));
    }

    // 2. Trang Chi tiết 1 Danh mục
    public function categoryDetail($madm) {
        $categories = $this->extractData($this->callApi('/api/danhmucs'));
        $allProducts = $this->extractData($this->callApi('/api/sanphams'));
        
        // Lọc ra các sản phẩm thuộc danh mục này
        $categoryProducts = collect($allProducts)->where('madm', $madm)->toArray();
        
        // Cần tạo thêm file giao diện: resources/views/category/show.blade.php
        return view('category.show', compact('categoryProducts', 'categories'));
    }

    // 3. Trang Chi tiết 1 Sản phẩm
    public function productDetail($masp) {
        // Gọi API lấy 1 sản phẩm
        $product = $this->extractData($this->callApi("/api/sanphams/{$masp}"));
        
        // Cần tạo thêm file giao diện: resources/views/product/show.blade.php
        return view('product.show', compact('product'));
    }
    // Xử lý luồng Tìm kiếm
public function search(Request $request) {
        $keyword = trim($request->query('query'));

        if (empty($keyword)) {
            return redirect()->route('home');
        }

        // Gọi API sang cổng 8000
        $response = $this->callApi('/api/sanphams/search?query=' . urlencode($keyword));
        
        // Trích xuất data an toàn
        $products = isset($response['data']) ? $response['data'] : [];

        return view('search.results', compact('products', 'keyword'));
    }
}

