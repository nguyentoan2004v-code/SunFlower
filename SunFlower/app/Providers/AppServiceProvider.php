<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Models\DanhMuc;
use Illuminate\Pagination\Paginator;

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
    
    View::composer('*', function ($view) {
        // Lấy tất cả các biến mà Controller đã truyền sang View
        $viewData = $view->getData();

        // Nếu Controller CHƯA truyền biến 'categories', mới lấy từ cache
        if (!array_key_exists('categories', $viewData)) {
            $view->with('categories', Cache::remember('danhmuc_all', 3600, function () {
                return DanhMuc::all();
            }));
        }
    });
}
}

