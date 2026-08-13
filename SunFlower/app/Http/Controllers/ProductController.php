<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham;

class ProductController extends Controller
{

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