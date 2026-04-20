<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SunFlower - Cửa hàng hoa tươi')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background font-sans antialiased text-foreground selection:bg-primary/20">
    <div class="relative flex min-h-screen flex-col flex-1 overflow-x-hidden">
        
        {{-- Chỉ hiển thị Header nếu không phải trang login hoặc register --}}
        @if(!request()->routeIs('login', 'register'))
            @include('partials.header')
        @endif

        <main class="flex-1 w-full mx-auto {{ request()->routeIs('login', 'register') ? '' : 'p-4 sm:p-6 lg:p-8' }}">
            @yield('content')
        </main>

        {{-- Chỉ hiển thị Footer nếu không phải trang login hoặc register --}}
        @if(!request()->routeIs('login', 'register'))
            @include('partials.footer')
        @endif
       
</main>

    </div>
</body>
</html>