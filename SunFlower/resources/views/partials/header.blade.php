<div class="bg-[#FEF9E7] py-2 border-b border-orange-100 text-center text-sm font-medium text-gray-600">
    🌻 Welcome to SunFlower - Premium Flowers Delivered Fresh Daily
</div>

<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="px-4 sm:px-6 lg:px-8 h-24 flex justify-between items-center relative">
        
        <a href="{{ route('home') }}" class="flex items-center gap-3 mr-6 group relative">
    
    <div class="absolute left-0 w-[50px] h-[50px] bg-[#FF6B35]/30 rounded-full blur-md opacity-0 group-hover:opacity-100 transition-opacity duration-700 ease-out z-0"></div>

    <div class="relative z-10 h-[50px] w-[50px] rounded-full overflow-hidden flex items-center justify-center border-[1.5px] border-orange-100 bg-white shrink-0 transition-all duration-500 ease-out group-hover:border-[#FF6B35]/50 group-hover:shadow-[0_4px_15px_rgba(255,107,53,0.15)]">
        <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780496206/5drg92D3VeOdSV5C41Lipg_2k_q40cvj.webp"
             alt="SunFlower Logo"
             class="w-full h-full object-cover scale-[1.35] group-hover:scale-[1.45] group-hover:rotate-6 transition-all duration-700 ease-out origin-center">
    </div>

    <div class="hidden sm:flex flex-col justify-center z-10">
        <div class="flex items-baseline mb-0.5">
            <span class="text-[30px] font-extrabold text-gray-700 tracking-tight transition-colors duration-300 group-hover:text-gray-900">Sun</span>
            <span class="text-[25px] font-extrabold text-[#FF6B35] tracking-tight transition-colors duration-300">Flower</span>
            
            <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B35] ml-0.5 opacity-0 group-hover:opacity-100 group-hover:translate-x-1.5 transition-all duration-500 ease-out"></span>
        </div>
        <span class="text-[10px] font-medium text-gray-500 tracking-[0.07em] leading-none transition-colors duration-300 group-hover:text-[#FF6B35]">Cửa hàng hoa tươi</span>
    </div>
</a>

        <nav class="hidden md:flex space-x-12 items-center">
            <a href="{{ route('home') }}" class="text-base font-semibold text-gray-700 hover:text-[#FF6B35] transition">Trang chủ</a>
            
            <a href="{{ route('categories.index') }}" class="text-base font-semibold text-gray-700 hover:text-[#FF6B35] transition">Danh mục</a>
            
            <a href="{{ route('about') }}" class="text-base font-semibold text-gray-700 hover:text-[#FF6B35] transition">Giới thiệu</a>
        </nav>

        <form action="{{ route('search') ?? '#' }}" method="GET" class="flex-1 max-w-md mx-8 relative">
            <input type="text" name="query" value="{{ request('query') }}" class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-[#FF6B35]/20 focus:border-[#FF6B35] outline-none transition" placeholder="Tìm kiếm đóa hoa của bạn...">
            
            <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#FF6B35] p-1 transition cursor-pointer" title="Tìm kiếm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
        </form>

        <div class="flex items-center space-x-6">

            <!-- Thông tin liên hệ & Giờ mở cửa -->
            <div class="hidden xl:flex items-center gap-3 border-r border-gray-100 pr-6">
                <div class="w-11 h-11 bg-orange-50 rounded-full flex items-center justify-center text-[#FF6B35]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <div class="flex flex-col">
                    <span class="text-base font-extrabold text-gray-900 leading-none">0987.654.321</span>
                    <span class="text-xs text-gray-500 font-medium mt-0.5">Mở cửa: 07:00 - 20:00</span>
                </div>
            </div>
            
              <a href="{{ route('cart.index') ?? '#' }}" class="text-gray-600 hover:text-[#FF6B35] relative transition">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold shadow-sm">{{ count(session('cart')) }}</span>
                @endif
            </a>

            @if(Auth::guard('khachhang')->check())
                <div class="relative group pl-4 border-l border-gray-200">
                    
                    <button class="flex items-center gap-3 focus:outline-none cursor-pointer">
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-sm text-gray-400 font-medium">Xin chào,</span>
                            <span class="text-base font-bold text-[#FF6B35]">{{ Auth::guard('khachhang')->user()->hoten }}</span>
                        </div>
                        <div class="w-12 h-12 bg-orange-50 rounded-full flex items-center justify-center text-[#FF6B35] border border-orange-100 group-hover:bg-[#FF6B35] group-hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
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
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                </div>
            @endif
            
        </div>
    </div>
</header>