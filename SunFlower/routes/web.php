<?php

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
   
    $ds_sanpham = DB::table('sanpham')->get();
    return view('index', ['ds_sanpham' => $ds_sanpham]);
});

// Giữ nguyên route checkout của mình lúc nãy
Route::get('/checkout', function () {
    return view('checkout');
});