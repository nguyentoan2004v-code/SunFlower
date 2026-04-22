<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;


// =====================
// USER
// =====================

// =====================
// 1. TRANG CHỦ & SẢN PHẨM
// =====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/danh-muc/{madm}', [HomeController::class, 'categoryDetail'])->name('category.show');
Route::get('/chi-tiet/{masp}', [HomeController::class, 'productDetail'])->name('product.show');
Route::get('/product/{masp}/image', [ProductController::class, 'showImage'])->name('product.image');
Route::get('/category/{madm}/image', [HomeController::class, 'showCategoryImage'])->name('category.image');
Route::get('/tat-ca-san-pham', [HomeController::class, 'allCategories'])->name('categories.index');
Route::get('/search', [HomeController::class, 'search'])->name('search');
Route::get('/gioi-thieu', [HomeController::class, 'about'])->name('about');
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
Route::get('/dat-hang-thanh-cong', [CartController::class, 'orderSuccess'])->name('checkout.success');

// =====================
// 5. ĐƠN HÀNG
// =====================
Route::get('/lich-su-don-hang', [OrderController::class, 'history'])->name('orders.history');
Route::get('/don-hang/{madon}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/don-hang/{madon}/huy', [OrderController::class, 'cancel'])->name('orders.cancel');


// =====================
// ADMIN
// =====================


// =====================
// 1 . ĐĂNG NHẬP QUẢN TRỊ
// =====================
Route::prefix('admin')->name('admin.')->group(function () {
    // Các route không cần login
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');

    // Các route CẦN login admin
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard'); // Bạn sẽ tạo file này sau
        })->name('dashboard');
        
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });
});