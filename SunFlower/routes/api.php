<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SanPhamController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KhachHangAuthController;
use App\Http\Controllers\Api\DonHangController;

// 1.1 Lấy danh sách sản phẩm
Route::get('/sanphams', [SanPhamController::class, 'index']);

// 1.2 Lấy chi tiết 1 sản phẩm (Truyền mã sản phẩm vào URL)
Route::get('/sanphams/{masp}', [SanPhamController::class, 'show']);

// 1.3 Lấy danh sách danh mục (Menu)
Route::get('/danhmucs', [DanhMucController::class, 'index']);

// [Giai đoạn 2.1] API Đăng nhập
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // [Giai đoạn 2.2] Lấy thông tin cá nhân
    Route::get('/me', [AuthController::class, 'me']);
    
    // Route yêu cầu khách phải đăng nhập
    Route::get('/customer/me', [KhachHangAuthController::class, 'me']);

    // THÊM DÒNG NÀY: API cập nhật thông tin cá nhân (Dùng PUT vì là hành động Sửa)
    Route::put('/customer/profile', [KhachHangAuthController::class, 'updateProfile']);

    // xem lịch sử mua hàng và chi tiết đơn hàng
    Route::get('/customer/orders', [\App\Http\Controllers\Api\DonHangController::class, 'myOrders']);
    Route::get('/customer/orders/{madon}', [\App\Http\Controllers\Api\DonHangController::class, 'showOrderDetails']);
});
// Route công khai cho khách
Route::post('/customer/register', [KhachHangAuthController::class, 'register']);
Route::post('/customer/login', [KhachHangAuthController::class, 'login']);
// API Thanh toán Lai (Hybrid Checkout) - Không bọc middleware để ai cũng vào được
Route::post('/customer/checkout', [\App\Http\Controllers\Api\DonHangController::class, 'checkout']);

