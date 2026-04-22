@extends('layouts.app')

@section('title', 'Trang chủ - SunFlower')

@section('content')
<div class="pb-12">
    <div class="max-w-7xl mx-auto mt-8 px-4 grid grid-cols-12 gap-6">
        
        <aside class="col-span-12 lg:col-span-3 bg-white border border-gray-100 rounded-3xl shadow-sm overflow-hidden self-start">
            <div class="bg-[#FF6B35] text-white p-4 font-bold text-[13px] flex items-center uppercase tracking-widest">
                <i class="fa-solid fa-bars mr-3 text-xs"></i> Danh mục
            </div>
            <ul class="text-sm font-bold text-gray-600">
                <li class="p-4 border-b border-gray-50 hover:bg-orange-50 hover:text-[#FF6B35] flex items-center transition cursor-pointer">
                    <i class="fa-solid fa-fire text-[#FF6B35] mr-4 w-4 text-center"></i> Best Sellers
                </li>
                <li class="p-4 border-b border-gray-50 hover:bg-orange-50 hover:text-[#FF6B35] flex items-center transition cursor-pointer">
                    <i class="fa-solid fa-sun text-amber-500 mr-4 w-4 text-center"></i> Hoa Chúc Mừng
                </li>
                <li class="p-4 border-b border-gray-50 hover:bg-orange-50 hover:text-[#FF6B35] flex items-center transition cursor-pointer">
                    <i class="fa-solid fa-ribbon text-gray-400 mr-4 w-4 text-center"></i> Hoa Chia Buồn
                </li>
                <li class="p-4 hover:bg-orange-50 hover:text-[#FF6B35] flex items-center transition cursor-pointer">
                    <i class="fa-solid fa-ticket text-[#FF6B35] mr-4 w-4 text-center"></i> E-Gift Voucher
                </li>
            </ul>
        </aside>

        <div class="col-span-12 md:col-span-8 lg:col-span-7">
            <div class="bg-gradient-to-br from-orange-50 to-white h-[400px] lg:h-[450px] rounded-3xl relative overflow-hidden flex items-center p-8 lg:p-12 border border-gray-100 shadow-sm group">
                <div class="z-10 max-w-sm">
                   <h2 class="text-4xl lg:text-5xl font-black text-gray-900 leading-none tracking-tighter uppercase italic">
                       SunFlower<br>
                       <span class="text-[#FF6B35] underline decoration-[#FF6B35] decoration-4 underline-offset-8">2026</span>
                   </h2>
                   <p class="text-gray-500 mt-6 text-sm lg:text-base font-medium italic leading-relaxed">
                       Mang ánh nắng rực rỡ gửi đến những người thân yêu của bạn.
                   </p>
                   <a href="#products" class="inline-block mt-8 bg-[#FF6B35] text-white px-8 py-3.5 rounded-full font-bold shadow-lg shadow-orange-200 hover:bg-orange-600 hover:-translate-y-1 transition duration-300 uppercase tracking-widest text-xs">
                       Khám phá ngay
                   </a>
                </div>
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-[#FF6B35]/10 rounded-full blur-3xl group-hover:scale-125 transition duration-1000"></div>
            </div>
        </div>

        <div class="col-span-4 lg:col-span-2 hidden md:flex flex-col space-y-4">
            @php $badges = [
                ['icon' => 'fa-hand-holding-dollar', 't' => 'Cam kết', 'd' => 'Giá cả hợp lý'],
                ['icon' => 'fa-motorcycle', 't' => 'Giao nhanh', 'd' => 'Nội thành 2H'],
                ['icon' => 'fa-circle-check', 't' => 'Đảm bảo', 'd' => 'Sạch, Tươi, Mới']
            ]; @endphp
            @foreach($badges as $b)
            <div class="bg-white border border-gray-100 p-4 lg:p-5 rounded-3xl text-center shadow-sm hover:border-orange-200 transition cursor-default flex-1 flex flex-col justify-center items-center group">
                <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center mb-2 group-hover:bg-[#FF6B35] transition duration-300">
                    <i class="fa-solid {{ $b['icon'] }} text-[#FF6B35] group-hover:text-white text-xl transition"></i>
                </div>
                <p class="font-bold text-[11px] uppercase text-gray-800 leading-none">{{ $b['t'] }}</p>
                <p class="text-[9px] text-gray-400 font-medium italic uppercase mt-1">{{ $b['d'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <main id="products" class="max-w-7xl mx-auto mt-20 px-4">
        <div class="flex justify-between items-end mb-8 pb-4 border-b border-gray-100">
            <h3 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight">Sản Phẩm Nổi Bật</h3>
            <a href="#" class="text-[#FF6B35] font-bold hover:underline transition uppercase tracking-widest text-xs flex items-center gap-1">
                Xem tất cả <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @if(isset($ds_sanpham) && count($ds_sanpham) > 0)
                @foreach($ds_sanpham as $sp)
                <div class="group bg-white border border-gray-100 rounded-3xl p-4 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-100/50 hover:-translate-y-1 relative flex flex-col">
                    
                    <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-50 mb-4 flex items-center justify-center">
                        @if(!empty($sp->hinhanh))
                            <img src="{{ route('product.image', $product->masp) }}" 
                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-500" 
                                 alt="{{ $sp->tensp }}">
                        @else
                            <i class="fa-solid fa-seedling text-5xl text-orange-200 group-hover:scale-125 transition duration-700"></i>
                        @endif
                        
                        <div class="absolute top-3 left-3 bg-[#FF6B35] text-white text-[10px] px-3 py-1 rounded-full font-bold shadow-sm uppercase">Hot</div>
                    </div>
                    
                    <h4 class="font-bold text-gray-800 text-sm mb-2 group-hover:text-[#FF6B35] transition-colors line-clamp-2 min-h-[40px]" title="{{ $sp->tensp }}">
                        {{ $sp->tensp }}
                    </h4>
                    
                    <div class="mt-auto">
                        <p class="text-lg font-extrabold text-[#FF6B35] mb-4">
                            {{ number_format($sp->giaban, 0, ',', '.') }} ₫
                        </p>
                        
                        <div class="flex flex-col gap-2 pt-3 border-t border-gray-50">
                            <div class="flex gap-2 h-10">
                                <a href="{{ route('product.show', $sp->masp) }}" 
                                   class="flex-1 bg-gray-50 text-gray-500 flex items-center justify-center rounded-xl font-bold text-xs hover:bg-gray-100 hover:text-gray-800 transition border border-gray-100">
                                    Chi tiết
                                </a>
                                
                                <a href="{{ route('cart.add', $sp->masp) }}" title="Thêm vào giỏ"
                                   class="w-10 shrink-0 bg-orange-50 text-[#FF6B35] flex items-center justify-center rounded-xl hover:bg-[#FF6B35] hover:text-white transition border border-orange-100">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </a>
                            </div>

                            <a href="{{ route('cart.buyNow', $sp->masp) }}" 
                               class="w-full h-10 bg-[#FF6B35] text-white flex items-center justify-center rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-lg shadow-orange-100 active:scale-95">
                                Mua ngay
                            </a>
                        </div>
                    </div>
                    
                </div>
                @endforeach
            @else
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-gray-200">
                    <i class="fa-solid fa-leaf text-4xl text-gray-200 mb-3"></i>
                    <p class="text-gray-500 font-medium">Hiện tại chưa có sản phẩm nào.</p>
                </div>
            @endif
        </div>
    </main>
</div>
@endsection