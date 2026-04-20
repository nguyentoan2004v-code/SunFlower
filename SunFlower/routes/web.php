<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;

// =====================
// 1. TRANG CHỦ & SẢN PHẨM
// =====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{madm}', [HomeController::class, 'categoryDetail'])->name('category.show');
Route::get('/chi-tiet/{masp}', [HomeController::class, 'productDetail'])->name('product.show');
Route::get('/tat-ca-san-pham', [HomeController::class, 'allCategories'])->name('categories.index');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// =====================
// 2. AUTH (ĐĂNG NHẬP / ĐĂNG KÝ)
// =====================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =====================
// 3. PROFILE (HỒ SƠ KHÁCH HÀNG)
// =====================
Route::prefix('profile')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// =====================
// 4. GIỎ HÀNG & THANH TOÁN
// =====================
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::get('/gio-hang/them/{masp}', [CartController::class, 'add'])->name('cart.add');
Route::get('/gio-hang/xoa/{masp}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/gio-hang/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/thanh-toan', [CartController::class, 'checkout'])->name('checkout');
Route::post('/dat-hang', [CartController::class, 'placeOrder'])->name('order.place');
Route::get('/mua-ngay/{masp}', [CartController::class, 'buyNow'])->name('cart.buyNow');