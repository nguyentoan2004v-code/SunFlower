<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckKhoRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->guard('nhanvien')->user();
        
        if (!$user || (!$user->hasRole('Quản lý Cửa hàng') && !$user->hasRole('Quản lý Kho hàng'))) {
            abort(403, 'Bạn không có quyền thao tác với Kho hàng!');
        }
        
        return $next($request);
    }
}
