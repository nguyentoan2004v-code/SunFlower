<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SanPhamController;
use App\Http\Controllers\Api\DanhMucController;

// 1.1 Lấy danh sách sản phẩm
Route::get('/sanphams', [SanPhamController::class, 'index']);

// 1.2 Lấy chi tiết 1 sản phẩm (Truyền mã sản phẩm vào URL)
Route::get('/sanphams/{masp}', [SanPhamController::class, 'show']);

// 1.3 Lấy danh sách danh mục (Menu)
Route::get('/danhmucs', [DanhMucController::class, 'index']);