<div class="bg-[#FEF9E7] py-2 border-b border-orange-100 text-center text-sm font-medium text-gray-600">
    🌻 Welcome to SunFlower - Premium Flowers Delivered Fresh Daily
</div>

<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 h-20 flex justify-between items-center relative">
        
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-[#FF6B35] rounded-full flex items-center justify-center text-white font-bold text-2xl shadow-inner">S</div>
            <span class="font-bold text-2xl text-[#FF6B35] tracking-tight uppercase">SunFlower</span>
        </a>

        <nav class="hidden md:flex space-x-10 items-center">
            <a href="{{ route('home') }}" class="font-semibold text-gray-700 hover:text-[#FF6B35] transition">Trang chủ</a>
            
            <div class="relative group">
                <a href="{{ route('categories.index') }}" class="font-semibold text-gray-700 hover:text-[#FF6B35] transition flex items-center gap-1 py-4">
                    Danh mục
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-[#FF6B35] transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
                
                <div class="absolute left-0 mt-0 w-56 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-left -translate-y-2 group-hover:translate-y-0 z-50">
                    <div class="py-2">
                        @if(isset($categories) && $categories->count() > 0)
                            @foreach($categories as $category)
                                <a href="{{ route('category.show', $category->madm) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-[#FF6B35] transition-colors">
                                    {{ $category->tendm }}
                                </a>
                            @endforeach
                        @else
                            <span class="block px-4 py-3 text-sm text-gray-500 text-center italic">Chưa có danh mục</span>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <form action="{{ route('search') ?? '#' }}" method="GET" class="flex-1 max-w-md mx-8 relative">
            <input type="text" name="query" value="{{ request('query') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm focus:ring-2 focus:ring-[#FF6B35]/20 focus:border-[#FF6B35] outline-none transition" placeholder="Tìm kiếm đóa hoa của bạn...">
            
            <button type="submit" class="absolute left-3 top-2.5 text-gray-400 hover:text-[#FF6B35] p-1 transition cursor-pointer" title="Tìm kiếm">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>

        <div class="flex items-center space-x-6">
            
              <a href="{{ route('cart.index') ?? '#' }}" class="text-gray-600 hover:text-[#FF6B35] relative transition">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold shadow-sm">{{ count(session('cart')) }}</span>
                @endif
            </a>

            @if(Auth::guard('khachhang')->check())
                <div class="relative group pl-4 border-l border-gray-200">
                    
                    <button class="flex items-center gap-3 focus:outline-none cursor-pointer">
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-xs text-gray-400 font-medium">Xin chào,</span>
                            <span class="text-sm font-bold text-[#FF6B35]">{{ Auth::guard('khachhang')->user()->hoten }}</span>
                        </div>
                        <div class="w-10 h-10 bg-orange-50 rounded-full flex items-center justify-center text-[#FF6B35] border border-orange-100 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                    </button>

                    <div class="absolute right-0 top-full mt-2 w-56 bg-white border border-gray-100 rounded-2xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0 z-50 overflow-hidden">
                        <div class="py-2">
                            
                            <a href="{{ route('profile.index') }}" class="px-4 py-2.5 text-sm text-gray-700 hover:bg-orange-50 hover:text-[#FF6B35] flex items-center gap-3 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                Thông tin tài khoản
                            </a>
                            
                            <a href="{{ route('orders.history') }}" class="px-4 py-2.5 text-sm text-gray-700 hover:bg-orange-50 hover:text-[#FF6B35] flex items-center gap-3 transition-colors">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                Lịch sử đơn hàng
                            </a>
                            
                            <div class="border-t border-gray-100 my-1"></div>
                            
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                    Đăng xuất
                                </button>
                            </form>
                            
                        </div>
                    </div>
                    
                </div>
           @else
                <div class="pl-4 border-l border-gray-200 flex items-center">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-[#FF6B35] transition" title="Đăng nhập">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                </div>
            @endif
            
        </div>
    </div>
</header>