<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // Thêm dòng này

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void

{
    // Chia sẻ biến $categories cho file partials.header
    // TẠM TẮT: Tránh gọi DB trực tiếp vì dự án đang dùng kiến trúc gọi qua API. 
    // Hơn nữa trong partials/header.blade.php hiện tại không sử dụng biến $categories này.
    // view()->composer('partials.header', function ($view) {
    //     $view->with('categories', \App\Models\DanhMuc::all());
    // });
}
}

