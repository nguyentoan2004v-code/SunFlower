<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;


// 1. Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. Luồng Đăng nhập (Khớp với /login trên trình duyệt)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/danh-muc/{madm}', [HomeController::class, 'categoryDetail'])->name('category.show');
// 3. Luồng Đăng ký (Khớp với /register trên trình duyệt)
Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register']);
// 4. Đăng xuất
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 5. Trang sản phẩm & chi tiết
Route::get('/', [HomeController::class, 'index'])->name('home');

// TRANG DANH MỤC SẢN PHẨM (Sửa lại tên cho đúng lỗi bro gặp)
// Route này sẽ khớp với {{ route('category.show', $category['madm']) }}
Route::get('/danh-muc/{madm}', [HomeController::class, 'categoryDetail'])->name('category.show');

// Trang chi tiết sản phẩm
Route::get('/chi-tiet/{masp}', [HomeController::class, 'productDetail'])->name('product.show');

// Trang tất cả sản phẩm (Nếu bro có dùng)
Route::get('/tat-ca-san-pham', [HomeController::class, 'allCategories'])->name('categories.index');


// Luồng Giỏ Hàng
Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
Route::get('/gio-hang/them/{masp}', [CartController::class, 'add'])->name('cart.add'); // Chính là dòng này!
Route::get('/gio-hang/xoa/{masp}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/thanh-toan', [CartController::class, 'checkout'])->name('checkout');
// Route Tìm kiếm
Route::get('/search', [\App\Http\Controllers\HomeController::class, 'search'])->name('search');