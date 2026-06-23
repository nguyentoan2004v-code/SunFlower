<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// 1. Import các Model cần thiết thay vì dùng ApiCaller
use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\DanhGia;
use App\Services\SemanticSearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HomeController extends Controller {
    
    // Đã xóa trait ApiCaller và hàm extractData()

    public function index() {
        // Lấy dữ liệu trực tiếp từ CSDL
        $categories = DanhMuc::all();
        $allProducts = SanPham::all(); 

        // Lấy 8 sản phẩm đầu tiên (Collection)
        $products = SanPham::orderBy('created_at', 'desc')->paginate(8); 

    // Random ảnh hero một cách an toàn (Sửa lại một chút để không bị lỗi với Paginator)
    $heroImage = null;
    if ($products->isNotEmpty()) {
        $heroImage = route('product.image', $products->getCollection()->random()->masp);
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
        // Thêm dòng này để lấy tên danh mục hiển thị ra giao diện
        $category = DanhMuc::where('madm', $madm)->firstOrFail();
        
        $categories = DanhMuc::all();
        
        // Đổi tên biến từ $categoryProducts thành $products để View có thể đọc được
        $products = \App\Models\SanPham::where('madm', $madm)->paginate(12);
        
        // Trả về view kèm $category và $products
        return view('category.show', compact('categories', 'category', 'products'));
    }

    // 3. Trang Chi tiết 1 Sản phẩm
    public function productDetail($masp) {
        // Nạp thêm tổng tồn kho của sản phẩm (withSum)
        $product = \App\Models\SanPham::with('danhmuc')
            ->withSum('lohangs', 'soluong_ton')
            ->find($masp);
        
        if (!$product) {
            abort(404, 'Sản phẩm không tồn tại!'); 
        }

        $relatedProducts = \App\Models\SanPham::where('madm', $product->madm)
                                  ->where('masp', '!=', $masp)
                                  ->take(4)
                                  ->get();

       // 1. Tính tổng lượt đánh giá và trung bình sao (CHỈ TÍNH NHỮNG ĐÁNH GIÁ ĐANG HIỆN)
        $totalReviews = \App\Models\DanhGia::where('masp', $masp)->where('trang_thai', 1)->count();
        $avgRating = $totalReviews > 0 ? round(\App\Models\DanhGia::where('masp', $masp)->where('trang_thai', 1)->avg('so_sao'), 1) : 0;

        // 2. Đếm số lượng cho bộ lọc (Giống Shopee) - Gộp thành 1 câu truy vấn groupBy để tối ưu hiệu năng
        $starCounts = \App\Models\DanhGia::where('masp', $masp)
            ->where('trang_thai', 1)
            ->selectRaw('so_sao, count(*) as count')
            ->groupBy('so_sao')
            ->pluck('count', 'so_sao')
            ->toArray();

        $countStars = [
            5 => $starCounts[5] ?? 0,
            4 => $starCounts[4] ?? 0,
            3 => $starCounts[3] ?? 0,
            2 => $starCounts[2] ?? 0,
            1 => $starCounts[1] ?? 0,
        ];
        // Đếm các đánh giá có chữ (không rỗng)
        $countComments = \App\Models\DanhGia::where('masp', $masp)->where('trang_thai', 1)->whereNotNull('binh_luan')->where('binh_luan', '!=', '')->count();

        // 3. Logic lấy danh sách Đánh Giá kèm Bộ Lọc
        // THÊM ĐIỀU KIỆN where('trang_thai', 1) VÀO ĐÂY
        $query = \App\Models\DanhGia::with('khachHang')->where('masp', $masp)->where('trang_thai', 1);
        $filter = request('filter');

        if (in_array($filter, ['1', '2', '3', '4', '5'])) {
            $query->where('so_sao', $filter);
        } elseif ($filter === 'comment') {
            $query->whereNotNull('binh_luan')->where('binh_luan', '!=', '');
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(5)->withQueryString();

        // Xây dựng và làm sạch nội dung mô tả chi tiết trước khi đưa ra View
        // Logic được đưa lên Controller để View chỉ có trách nhiệm hiển thị, không xử lý
        $finalContent = null;
        if (!empty($product->mota_chitiet)) {
            // Build URL ảnh đúng định dạng (Cloudinary hoặc local storage)
            $imgUrl = str_starts_with($product->hinhanh, 'http')
                ? $product->hinhanh
                : asset('storage/' . ltrim($product->hinhanh, '/'));

            // Dùng e() để escape tensp trong attribute alt — tránh XSS thứ cấp
            $imgHtml = '<img src="' . $imgUrl . '" alt="' . e($product->tensp) . '"'
                . ' class="w-full max-w-md mx-auto rounded-2xl shadow-md my-8 block border border-gray-100">';

            $rawContent   = str_replace('[anh_hoa]', $imgHtml, $product->mota_chitiet);

            // HTMLPurifier làm sạch toàn bộ HTML:
            // - Loại bỏ attribute nguy hiểm (onerror, onclick, onload...)
            // - Chặn javascript: và data: trong href/src
            // - Giữ nguyên cấu trúc HTML hợp lệ theo whitelist trong HtmlPurifierService
            $finalContent = app(\App\Services\HtmlPurifierService::class)->purify($rawContent);
        }

        return view('product.show', compact(
            'product', 'relatedProducts', 'reviews',
            'totalReviews', 'avgRating', 'countStars', 'countComments',
            'finalContent'
        ));
    }

    // =========================================================
    // Xử lý luồng Tìm kiếm (Semantic Search + Fallback LIKE)
    // =========================================================
    public function search(Request $request) {
        $keyword = trim($request->query('query'));

        if (empty($keyword)) {
            return redirect()->route('home');
        }

        // ---------------------------------------------------
        // Khởi tạo SemanticSearchService
        // ---------------------------------------------------
        $semanticService = app(SemanticSearchService::class);

        // ---------------------------------------------------
        // Tải toàn bộ sản phẩm 1 lần (tránh N+1 queries)
        // SemanticSearch cần duyệt qua tất cả để tính similarity
        // ---------------------------------------------------
        $allProducts = SanPham::all();

        // ---------------------------------------------------
        // Gọi Semantic Search (tự động fallback nếu API lỗi)
        // ---------------------------------------------------
        $result      = $semanticService->search($keyword, $allProducts);
        $rawProducts = $result['products'];   // Mảng sản phẩm đã sort
        $searchType  = $result['search_type']; // 'semantic' | 'fallback'

        // ---------------------------------------------------
        // Phân trang thủ công vì SemanticSearch trả về array,
        // không phải Eloquent Builder
        // ---------------------------------------------------
        $perPage     = 12;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $collection  = new Collection($rawProducts);
        $sliced      = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $products = new LengthAwarePaginator(
            $sliced,
            $collection->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Truyền search_type ra View để hiển thị badge "AI" hoặc "Thường"
        return view('search.results', compact('products', 'keyword', 'searchType'));
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