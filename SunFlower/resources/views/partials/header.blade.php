<div class="bg-[#FEF9E7] py-2 border-b border-orange-100 text-center text-sm font-medium text-gray-600">
    🌻 Welcome to SunFlower - Premium Flowers Delivered Fresh Daily
</div>

<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 h-20 flex justify-between items-center">
        
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-[#FF6B35] rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-inner">S</div>
            <span class="font-bold text-2xl text-[#FF6B35] tracking-tight uppercase">SunFlower</span>
        </a>

        <nav class="hidden md:flex space-x-10">
            <a href="{{ route('home') }}" class="font-semibold text-gray-700 hover:text-[#FF6B35] transition">Trang chủ</a>
            <a href="{{ route('categories.index') }}" class="font-semibold text-gray-700 hover:text-[#FF6B35] transition">Danh mục</a>
        </nav>

        <form action="{{ route('search') }}" method="GET" class="flex-1 max-w-md mx-8 relative">
            <input type="text" name="query" value="{{ request('query') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:ring-2 focus:ring-[#FF6B35]/20 focus:border-[#FF6B35] outline-none transition" placeholder="Tìm kiếm đóa hoa của bạn...">
            
            <button type="submit" class="absolute left-3 top-2.5 text-gray-400 hover:text-[#FF6B35] p-1 transition cursor-pointer" title="Tìm kiếm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>

        <div class="flex items-center space-x-6">
            
            <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-[#FF6B35] relative transition">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-sm">{{ count(session('cart')) }}</span>
                @endif
            </a>

            @if(session()->has('api_token'))
                {{-- KHI ĐÃ ĐĂNG NHẬP --}}
                <div class="flex items-center gap-3">
                    <div class="flex flex-col text-right">
                        <span class="text-xs text-gray-400 font-medium">Xin chào,</span>
                        <span class="text-sm font-bold text-[#FF6B35]">{{ session('user_info')['hoten'] ?? 'Bro' }}</span>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="p-2 bg-gray-50 rounded-full hover:bg-red-50 text-gray-500 hover:text-red-500 transition" title="Đăng xuất">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </button>
                    </form>
                </div>
            @else
                {{-- KHI CHƯA ĐĂNG NHẬP --}}
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-[#FF6B35] transition">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </a>
            @endif

        </div>
    </div>
</header>