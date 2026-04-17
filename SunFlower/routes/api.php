<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SanPhamController;
use App\Http\Controllers\Api\DanhMucController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KhachHangAuthController;
use App\Http\Controllers\Api\DonHangController;

// ==========================================
// --- API PUBLIC (KHÔNG CẦN ĐĂNG NHẬP) ---
// ==========================================

// 1. Luồng Sản Phẩm (Search phải nằm trên cùng)
Route::get('/sanphams/search', [SanPhamController::class, 'search']);
Route::get('/sanphams', [SanPhamController::class, 'index']);
Route::get('/sanphams/{masp}', [SanPhamController::class, 'show']);
Route::get('/danhmucs', [DanhMucController::class, 'index']);

// 3. API Khách hàng vãng lai (Đăng nhập, Đăng ký, Thanh toán không cần acc)
Route::post('/customer/register', [KhachHangAuthController::class, 'register']);
Route::post('/customer/login', [KhachHangAuthController::class, 'login']);
Route::post('/customer/checkout', [DonHangController::class, 'checkout']);

// 4. API Admin đăng nhập
Route::post('/login', [AuthController::class, 'login']);


// ==========================================
// --- API PRIVATE (BẮT BUỘC CÓ TOKEN) ---
// ==========================================
Route::middleware('auth:sanctum')->group(function () {
    
    // Admin
    Route::get('/me', [AuthController::class, 'me']);
    
    // Khách hàng
    Route::get('/customer/me', [KhachHangAuthController::class, 'me']);
    Route::put('/customer/profile', [KhachHangAuthController::class, 'updateProfile']);
    Route::post('/customer/logout', [KhachHangAuthController::class, 'logout']); 
    
    // Đơn hàng của khách
    Route::get('/customer/orders', [DonHangController::class, 'myOrders']);
    Route::get('/customer/orders/{madon}', [DonHangController::class, 'showOrderDetails']);
    
});