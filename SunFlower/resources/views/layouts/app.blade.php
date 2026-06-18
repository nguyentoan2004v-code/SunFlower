<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SunFlower - Cửa hàng hoa tươi')</title>
    <link rel="icon" type="image/png" href="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp">
    <link rel="shortcut icon" type="image/png" href="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background font-sans antialiased text-foreground selection:bg-primary/20">
    <div class="relative flex min-h-screen flex-col flex-1 overflow-x-hidden">
        
        {{-- Chỉ hiển thị Header nếu không phải trang auth (login, register, quên/đặt lại mật khẩu) --}}
        @if(!request()->routeIs('login', 'register', 'password.*'))
            @include('partials.header')
        @endif

        <main class="flex-1 w-full mx-auto {{ request()->routeIs('login', 'register', 'password.*') ? '' : 'p-4 sm:p-6 lg:p-8' }}">
            @yield('content')
        </main>

        {{-- Chỉ hiển thị Footer nếu không phải trang auth (login, register, quên/đặt lại mật khẩu) --}}
        @if(!request()->routeIs('login', 'register', 'password.*'))
            @include('partials.footer')
        @endif
       
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Loading state cho các form có thuộc tính data-loading
            document.querySelectorAll('form[data-loading]').forEach(form => {
                form.addEventListener('submit', function() {
                    const btn = this.querySelector('button[type="submit"]');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '⏳ Đang xử lý...';
                    }
                });
            });
        });
    </script>
</body>
</html>