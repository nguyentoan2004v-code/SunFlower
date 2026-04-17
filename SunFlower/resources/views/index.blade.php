<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SunFlower - Tỏa Sáng Ngàn Hoa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Quicksand', sans-serif; }</style>
</head>
<body class="bg-[#FFFBF2] text-gray-800">

    <div class="bg-amber-600 text-white py-1.5 px-10 flex justify-between items-center text-[11px] font-bold uppercase tracking-wider">
        <div>Vietnamese | English</div>
        <div class="flex space-x-6">
            <a href="#" class="hover:text-amber-200">Giới thiệu</a>
            <a href="#" class="hover:text-amber-200">Tin tức</a>
            <a href="#" class="hover:text-amber-200">Hệ thống cửa hàng</a>
            <a href="#" class="hover:text-amber-200"><i class="fa-regular fa-user mr-1.5"></i> Đăng nhập</a>
        </div>
    </div>

    <header class="bg-white pt-5 px-10 shadow-sm sticky top-0 z-50">
        <div class="max-w-full mx-auto flex items-center justify-between pb-5">
            <div class="w-[220px]">
                <h1 class="text-amber-500 text-4xl font-black italic tracking-tighter drop-shadow-sm">SUN<span class="text-orange-600">FLOWER</span></h1>
                <p class="text-[9px] text-amber-700/50 mt-1 uppercase tracking-[0.2em] font-bold">Blooming your life</p>
            </div>

            <div class="flex-1 max-w-2xl px-12">
                <div class="relative flex items-center group">
                    <input type="text" placeholder="Tìm kiếm niềm vui tại đây..." 
                           class="w-full border-2 border-amber-100 rounded-full py-2.5 px-8 focus:border-orange-400 focus:outline-none text-sm transition-all shadow-inner group-hover:shadow-md">
                    <button class="absolute right-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white p-2 px-6 rounded-full hover:scale-105 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-10">
                <div class="flex items-center space-x-3 group">
                    <div class="bg-amber-50 p-2.5 rounded-full group-hover:bg-amber-100 transition"><i class="fa-solid fa-location-dot text-xl text-amber-600"></i></div>
                    <div class="leading-tight">
                        <p class="text-[11px] italic text-gray-400">Giao từ</p>
                        <p class="text-sm font-bold text-gray-700">Chi nhánh TP.HCM</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 group border-l pl-8">
                    <div class="bg-orange-50 p-2.5 rounded-full group-hover:bg-orange-100 transition"><i class="fa-solid fa-phone text-xl text-orange-600"></i></div>
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-orange-600">1800 2026</p>
                        <p class="text-[10px] text-gray-400">Hỗ trợ 24/7</p>
                    </div>
                </div>
                <a href="/checkout" class="relative bg-amber-50 p-3 rounded-full hover:bg-amber-500 group transition">
                    <i class="fa-solid fa-cart-shopping text-2xl text-amber-600 group-hover:text-white"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center border-2 border-white shadow-md">0</span>
                </a>
            </div>
        </div>

        <div class="h-1.5 flex w-full">
            @for ($i = 0; $i < 20; $i++)
                <div class="flex-1 h-full {{ $i % 2 == 0 ? 'bg-amber-500' : 'bg-orange-500' }} mx-[3px] rounded-full opacity-80"></div>
            @endfor
        </div>
    </header>

    <nav class="bg-white border-b py-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-center space-x-12 text-[13px] font-bold text-gray-600 uppercase">
            <a href="#" class="flex items-center hover:text-amber-600 transition"><i class="fa-solid fa-gift mr-2.5 opacity-50"></i> Hoa Quà Tặng</a>
            <a href="#" class="flex items-center hover:text-amber-600 transition"><i class="fa-solid fa-leaf mr-2.5 opacity-50"></i> Hoa Chậu Design</a>
            <a href="#" class="flex items-center hover:text-amber-600 transition"><i class="fa-solid fa-spa mr-2.5 opacity-50"></i> Lan Hồ Điệp</a>
            <a href="#" class="flex items-center text-orange-600 transition"><i class="fa-solid fa-wand-magic-sparkles mr-2.5"></i> Deal Hời Mỗi Ngày</a>
            <a href="#" class="flex items-center hover:text-amber-600 transition"><i class="fa-solid fa-truck-fast mr-2.5 opacity-50"></i> Ship Nhanh 2H</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-8 px-4 grid grid-cols-12 gap-6">
        
        <aside class="col-span-2 hidden lg:block bg-white border border-amber-100 rounded-[2rem] shadow-lg overflow-hidden self-start">
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white p-4 font-bold text-sm text-center tracking-widest">
                DANH MỤC HOA
            </div>
            <ul class="text-[13px] font-bold text-gray-600">
                <li class="p-4 border-b hover:bg-amber-50 hover:text-amber-600 flex items-center transition"><i class="fa-solid fa-fire text-red-500 mr-3"></i> Best Sellers</li>
                <li class="p-4 border-b hover:bg-amber-50 flex items-center transition"><i class="fa-solid fa-sun text-amber-500 mr-3"></i> Hoa Chúc Mừng</li>
                <li class="p-4 border-b hover:bg-amber-50 flex items-center transition"><i class="fa-solid fa-ribbon text-orange-400 mr-3"></i> Hoa Chia Buồn</li>
                <li class="p-4 border-b hover:bg-amber-50 flex items-center transition"><i class="fa-solid fa-heart text-pink-400 mr-3"></i> Hoa Cưới</li>
                <li class="p-4 border-b hover:bg-amber-50 flex items-center transition"><i class="fa-solid fa-vial text-purple-400 mr-3"></i> Bình Hoa</li>
                <li class="p-4 border-b hover:bg-amber-50 flex items-center font-bold text-orange-600 transition"><i class="fa-solid fa-ticket text-orange-600 mr-3"></i> E-Gift Voucher</li>
            </ul>
        </aside>

        <div class="col-span-12 lg:col-span-8">
            <div class="bg-gradient-to-br from-amber-400 via-orange-300 to-white h-[450px] rounded-[3rem] relative overflow-hidden flex items-center p-16 border shadow-xl group">
                <div class="z-10 max-w-md">
                   <span class="bg-white/80 backdrop-blur-md text-orange-600 px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">Collection 2026</span>
                   <h2 class="text-6xl font-black text-gray-800 mt-6 leading-[1.1]">RỰC RỠ <br><span class="text-amber-700 underline decoration-orange-400 decoration-4 underline-offset-8">ÁNH DƯƠNG</span></h2>
                   <p class="text-gray-700 mt-6 text-base font-medium">Khám phá bộ sưu tập hoa hướng dương mới nhất với ưu đãi 20%.</p>
                   <button class="mt-10 bg-gray-900 text-white px-10 py-4 rounded-full font-black shadow-2xl hover:bg-orange-600 transition-all hover:scale-105 active:scale-95">MUA NGAY</button>
                </div>
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-white/20 rounded-full blur-3xl group-hover:bg-amber-200/40 transition duration-1000"></div>
            </div>
        </div>

        <div class="col-span-2 hidden lg:flex flex-col space-y-4">
            <div class="bg-white border-2 border-amber-100 p-5 rounded-[2rem] text-center shadow-md hover:border-amber-400 transition cursor-default">
                <div class="bg-amber-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-hand-holding-dollar text-2xl text-amber-600"></i></div>
                <p class="font-black text-[11px] uppercase">Cam kết</p>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Giá hợp lý</p>
            </div>
            <div class="bg-white border-2 border-orange-100 p-5 rounded-[2rem] text-center shadow-md hover:border-orange-400 transition cursor-default">
                <div class="bg-orange-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-motorcycle text-2xl text-orange-600"></i></div>
                <p class="font-black text-[11px] uppercase text-orange-700">Giao nhanh</p>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">nội thành</p>
            </div>
            <div class="bg-white border-2 border-green-100 p-5 rounded-[2rem] text-center shadow-md hover:border-green-400 transition cursor-default">
                <div class="bg-green-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-leaf text-2xl text-green-600"></i></div>
                <p class="font-black text-[11px] uppercase text-green-700">Đảm bảo</p>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter">Hoa tươi mới</p>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto mt-16 px-4 mb-24">
        <div class="flex justify-between items-center mb-10 pb-3 border-b-4 border-amber-500/20">
            <h3 class="text-3xl font-black text-amber-700 uppercase italic tracking-tighter">Hoa Tặng & Dịch Vụ</h3>
            <a href="#" class="bg-amber-50 text-amber-700 px-5 py-2 rounded-full text-xs font-bold hover:bg-amber-500 hover:text-white transition">Xem tất cả <i class="fa-solid fa-chevron-right ml-1"></i></a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-8">
            @if(isset($ds_sanpham))
                @foreach($ds_sanpham as $sp)
                <div class="bg-white rounded-[2.5rem] p-5 shadow-sm hover:shadow-2xl transition-all duration-500 border border-transparent hover:border-amber-100 group relative overflow-hidden">
                    <div class="h-48 bg-amber-50 rounded-[2rem] mb-5 flex items-center justify-center relative overflow-hidden">
                        <i class="fa-solid fa-seedling text-5xl text-amber-200 group-hover:scale-125 transition duration-700"></i>
                        <div class="absolute top-4 left-4 bg-red-500 text-white text-[9px] px-3 py-1 rounded-full font-black shadow-lg">HOT</div>
                    </div>
                    <h4 class="text-sm font-bold text-gray-700 h-10 line-clamp-2 leading-tight group-hover:text-amber-700 transition">{{ $sp->tensp }}</h4>
                    <div class="mt-6 flex flex-col items-center">
                        <p class="text-xl font-black text-orange-600">{{ number_format($sp->giaban, 0, ',', '.') }}đ</p>
                        <button class="w-full mt-4 bg-amber-500 text-white py-3 rounded-2xl text-[11px] font-black uppercase shadow-lg shadow-amber-200 hover:bg-gray-900 transition-colors">
                            GIỎ HÀNG +
                        </button>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </main>

</body>
</html>