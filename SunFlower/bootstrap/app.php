<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminMiddleware::class,
            'check.kho.role' => \App\Http\Middleware\CheckKhoRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Trang 404 - Không tìm thấy
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Không tìm thấy trang này.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Trang 419 - CSRF Token hết hạn
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.'], 419);
            }
            return response()->view('errors.419', [], 419);
        });

        // Trang 403 - Cấm truy cập
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Bạn không có quyền truy cập.'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        // Trang 500 - Lỗi server (chỉ khi APP_DEBUG=false)
        $exceptions->render(function (\Throwable $e, $request) {
            if (!config('app.debug') && !$e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Hệ thống đang gặp sự cố. Vui lòng thử lại sau.'], 500);
                }
                return response()->view('errors.500', [], 500);
            }
        });
    })->create();
    
