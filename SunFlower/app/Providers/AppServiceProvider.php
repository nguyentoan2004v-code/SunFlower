<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View; 
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
            
            // Nếu Controller CHƯA truyền biến 'categories' (như ở các trang con ngoài trang chủ), 
            // thì mới tự động lấy từ DB lên để hiển thị Menu
            if (!array_key_exists('categories', $viewData)) {
                $view->with('categories', DanhMuc::all());
            }
        });
}
}

