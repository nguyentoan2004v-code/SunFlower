<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SunFlower - Flower Your Life</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Quicksand', sans-serif; }</style>
</head>
<body class="bg-[#F8FAF9] text-slate-800">

    <div class="bg-[#00703c] text-white py-2 px-10 flex justify-between items-center text-[11px] font-bold uppercase tracking-wider">
        <div>Vietnamese | English</div>
        <div class="flex space-x-6">
            <a href="#" class="hover:text-green-200 flex items-center"><i class="fa-regular fa-comment-dots mr-1.5"></i> About SunFlower</a>
            <a href="#" class="hover:text-green-200 flex items-center"><i class="fa-regular fa-calendar-check mr-1.5"></i> Tin nổi bật</a>
            <a href="#" class="hover:text-green-200 flex items-center"><i class="fa-solid fa-store mr-1.5"></i> Tìm cửa hàng</a>
            <a href="#" class="hover:text-green-200"><i class="fa-regular fa-user mr-1.5"></i> Đăng nhập</a>
        </div>
    </div>

    <header class="bg-white pt-5 px-10 shadow-sm sticky top-0 z-50">
        <div class="max-w-full mx-auto flex items-center justify-between pb-5">
            <div class="w-[200px]">
                <h1 class="text-[#008542] text-3xl font-black italic leading-none">SUN<span class="text-orange-500">FLOWER</span></h1>
                <p class="text-[9px] text-slate-400 mt-1 uppercase tracking-[0.2em] font-bold">Flower your life</p>
            </div>

            <div class="flex-1 max-w-xl px-12">
                <div class="relative flex items-center group">
                    <input type="text" placeholder="Tìm kiếm sản phẩm..." 
                           class="w-full border-2 border-green-700 rounded-full py-2.5 px-8 focus:border-orange-400 outline-none text-sm transition-all shadow-inner">
                    <button class="absolute right-0 bg-[#008542] text-white h-full px-6 rounded-r-full hover:bg-green-800 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-10">
                <div class="flex items-center space-x-2 group">
                    <i class="fa-solid fa-location-dot text-2xl text-slate-300 group-hover:text-[#008542] transition"></i>
                    <div class="leading-tight">
                        <p class="text-[12px] italic text-slate-400 font-bold uppercase">Giao từ</p>
                        <p class="text-xs font-bold text-slate-700">STU Branch</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2 group border-l pl-8">
                    <i class="fa-solid fa-phone text-2xl text-slate-300 group-hover:text-[#008542] transition"></i>
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-[#008542]">1800 1143</p>
                        <p class="text-[10px] italic text-slate-400 font-bold uppercase">08:00 - 20:00</p>
                    </div>
                </div>
                <a href="/checkout" class="relative group bg-orange-50 p-3 rounded-full hover:bg-orange-500 transition">
                    <i class="fa-solid fa-cart-shopping text-2xl text-orange-600 group-hover:text-white transition"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-sm">0</span>
                </a>
            </div>
        </div>

        <div class="h-1 flex w-full">
            @for ($i = 0; $i < 18; $i++)
                <div class="flex-1 h-full {{ $i % 2 == 0 ? 'bg-[#008542]' : 'bg-orange-500' }} mx-[2px] rounded-full"></div>
            @endfor
        </div>
    </header>

    <nav class="bg-white border-b py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-center space-x-12 text-[13px] font-bold text-slate-600 uppercase">
            <a href="#" class="flex items-center hover:text-[#008542] transition"><i class="fa-solid fa-gift mr-2.5 text-slate-300"></i> Hoa tặng</a>
            <a href="#" class="flex items-center hover:text-[#008542] transition"><i class="fa-solid fa-leaf mr-2.5 text-slate-300"></i> Hoa chậu thiết kế</a>
            <a href="#" class="flex items-center hover:text-[#008542] transition"><i class="fa-solid fa-spa mr-2.5 text-slate-300"></i> Lan Hồ Điệp</a>
            <a href="#" class="flex items-center text-orange-600 transition"><i class="fa-solid fa-wand-magic-sparkles mr-2.5"></i> Hoa xinh giá tốt</a>
            <a href="#" class="flex items-center hover:text-[#008542] transition"><i class="fa-solid fa-motorcycle mr-2.5 text-slate-300"></i> HCM giao nhanh 2h</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-8 px-4 grid grid-cols-12 gap-6">
        <aside class="col-span-2 hidden lg:block bg-white border border-slate-100 rounded-[2rem] shadow-sm overflow-hidden self-start">
            <div class="bg-[#008542] text-white p-4 font-bold text-[13px] flex items-center uppercase tracking-widest"><i class="fa-solid fa-bars mr-3 text-xs"></i> Danh mục</div>
            <ul class="text-[13px] font-bold text-slate-600">
                <li class="p-4 border-b hover:bg-green-50 flex items-center transition cursor-pointer"><i class="fa-solid fa-fire text-orange-500 mr-4 w-4"></i> Best Sellers</li>
                <li class="p-4 border-b hover:bg-green-50 flex items-center transition cursor-pointer"><i class="fa-solid fa-sun text-amber-500 mr-4 w-4"></i> Hoa Chúc Mừng</li>
                <li class="p-4 border-b hover:bg-green-50 flex items-center transition cursor-pointer"><i class="fa-solid fa-ribbon text-slate-300 mr-4 w-4"></i> Hoa Chia Buồn</li>
                <li class="p-4 border-b hover:bg-green-50 flex items-center transition cursor-pointer"><i class="fa-solid fa-ticket text-orange-600 mr-4 w-4"></i> E-Gift Voucher</li>
            </ul>
        </aside>

        <div class="col-span-12 lg:col-span-8">
            <div class="bg-gradient-to-br from-green-50 to-white h-[450px] rounded-[3rem] relative overflow-hidden flex items-center p-16 border-2 border-white shadow-inner group">
                <div class="z-10 max-w-sm">
                   <h2 class="text-6xl font-black text-[#008542] leading-none tracking-tighter uppercase italic">SunFlower<br><span class="text-orange-500 underline decoration-[#008542] decoration-4 underline-offset-8">2026</span></h2>
                   <p class="text-slate-500 mt-10 text-base font-bold italic leading-relaxed">Mang ánh nắng rực rỡ gửi đến những người thân yêu của bạn.</p>
                   <button class="mt-10 bg-[#008542] text-white px-10 py-4 rounded-full font-black shadow-xl hover:bg-orange-500 hover:scale-105 transition duration-300 uppercase tracking-widest text-xs">Khám phá ngay</button>
                </div>
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#008542]/5 rounded-full blur-3xl group-hover:scale-125 transition duration-1000"></div>
            </div>
        </div>

        <div class="col-span-2 hidden lg:flex flex-col space-y-4">
            @php $badges = [
                ['icon' => 'fa-hand-holding-dollar', 'c' => 'border-orange-100', 'ic' => 'text-orange-500', 't' => 'Cam kết', 'd' => 'Giá cả hợp lý'],
                ['icon' => 'fa-motorcycle', 'c' => 'border-green-600', 'ic' => 'text-green-700', 't' => 'Giao nhanh', 'd' => 'nội thành'],
                ['icon' => 'fa-circle-check', 'c' => 'border-green-600', 'ic' => 'text-green-700', 't' => 'Đảm bảo', 'd' => 'Sạch, Tươi, Mới']
            ]; @endphp
            @foreach($badges as $b)
            <div class="bg-white border-2 {{ $b['c'] }} p-5 rounded-[2.5rem] text-center shadow-sm hover:shadow-md transition cursor-default">
                <i class="fa-solid {{ $b['icon'] }} {{ $b['ic'] }} text-3xl mb-1 transition duration-300"></i>
                <p class="font-black text-[11px] uppercase text-slate-700 leading-none mt-2">{{ $b['t'] }}</p>
                <p class="text-[9px] text-slate-400 font-bold italic uppercase mt-1">{{ $b['d'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <main class="max-w-7xl mx-auto mt-20 px-4 mb-24">
        <div class="flex justify-between items-end mb-10 pb-4 border-b-4 border-green-100">
            <h3 class="text-4xl font-black text-[#008542] uppercase italic tracking-tighter">Hoa Tặng & Dịch Vụ</h3>
            <a href="#" class="bg-green-50 text-[#008542] px-6 py-2.5 rounded-full text-[11px] font-black hover:bg-[#008542] hover:text-white transition uppercase tracking-widest">Xem tất cả <i class="fa-solid fa-chevron-right ml-1"></i></a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
            @if(isset($ds_sanpham))
                @foreach($ds_sanpham as $sp)
                <div class="bg-white rounded-[2.5rem] p-6 shadow-sm hover:shadow-2xl transition-all duration-500 border border-transparent hover:border-green-100 group relative overflow-hidden">
                    <div class="h-48 bg-slate-50 rounded-[2rem] mb-6 flex items-center justify-center relative overflow-hidden">
                        <i class="fa-solid fa-seedling text-5xl text-green-100 group-hover:scale-125 transition duration-700"></i>
                        <div class="absolute top-4 left-4 bg-red-500 text-white text-[9px] px-3 py-1 rounded-full font-black shadow-lg uppercase">Hot</div>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700 h-10 line-clamp-2 leading-tight group-hover:text-[#008542] transition">{{ $sp->tensp }}</h4>
                    <div class="mt-8 flex flex-col items-center">
                        <p class="text-2xl font-black text-orange-600">{{ number_format($sp->giaban, 0, ',', '.') }}đ</p>
                        <button class="w-full mt-5 bg-[#008542] text-white py-4 rounded-2xl text-[11px] font-black uppercase shadow-lg shadow-green-100 hover:bg-slate-900 transition-all active:scale-95">
                            Thêm Vào Giỏ +
                        </button>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </main>

    <footer class="bg-[#ECF2EF] pt-20 pb-10 px-10 rounded-t-[4rem] border-t border-green-100">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <div class="space-y-6">
                <h2 class="text-[#008542] text-3xl font-black italic">SUN<span class="text-orange-500">FLOWER</span></h2>
                <p class="text-sm text-slate-600 leading-relaxed font-medium">Tiên phong mang nghệ thuật hoa tươi đến mọi gia đình Việt.</p>
                <div class="flex space-x-4">
                    <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#008542] shadow-sm hover:bg-[#008542] hover:text-white transition-all"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-[#008542] shadow-sm hover:bg-[#008542] hover:text-white transition-all"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
            <div>
                <h4 class="text-slate-800 font-black text-sm uppercase tracking-widest mb-6 border-l-4 border-orange-500 pl-3">Sản phẩm</h4>
                <ul class="space-y-3 text-sm font-bold text-slate-500">
                    <li><a href="#" class="hover:text-[#008542] transition-all">Hoa hướng dương</a></li>
                    <li><a href="#" class="hover:text-[#008542] transition-all">Lan hồ điệp</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-slate-800 font-black text-sm uppercase tracking-widest mb-6 border-l-4 border-[#008542] pl-3">Hỗ trợ</h4>
                <ul class="space-y-3 text-sm font-bold text-slate-500">
                    <li><a href="#" class="hover:text-[#008542] transition-all">Chính sách bảo mật</a></li>
                    <li><a href="#" class="hover:text-[#008542] transition-all">Hướng dẫn mua hàng</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-slate-800 font-black text-sm uppercase tracking-widest mb-6 border-l-4 border-orange-500 pl-3">Liên hệ</h4>
                <div class="bg-white p-5 rounded-[2rem] shadow-sm border border-green-50">
                    <p class="text-xs font-black text-[#008542] uppercase mb-1 tracking-tighter">Hotline hỗ trợ</p>
                    <p class="text-xl font-black text-orange-600 leading-none">1800 1143</p>
                </div>
                <p class="text-[11px] text-slate-400 font-bold mt-6 uppercase text-center md:text-left">© 2026 SUNFLOWER BY LE CHI PHONG</p>
            </div>
        </div>
    </footer>

</body>
</html>