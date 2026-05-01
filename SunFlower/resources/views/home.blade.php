@extends('layouts.app')

@section('title', 'Trang chủ - SunFlower')

@section('content')
<div class="bg-gray-50 space-y-20 pb-20">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<section class="relative w-screen left-1/2 -translate-x-1/2 h-[550px] bg-gray-900 overflow-hidden">
    <div class="swiper heroSwiper w-full h-full">
        <div class="swiper-wrapper">
            @if(isset($products) && $products->count() > 0)
                @foreach($products->take(5) as $product)
                    @php
                        $prodImage = !empty($product->hinhanh)
                                     ? route('product.image', $product->masp)
                                     : asset('images/bg-sunflower.jpg');
                    @endphp
                    <div class="swiper-slide relative w-full h-full flex items-center bg-cover bg-center" style="background-image: url('{{ $prodImage }}');">
                        <div class="absolute inset-0 bg-black/50"></div> <div class="relative z-10 max-w-7xl mx-auto px-8 md:px-12 w-full flex flex-col items-start justify-center text-white pt-12">
                            <div class="max-w-2xl text-left"> <span class="inline-block bg-[#FF6B35] text-white text-[10px] font-bold px-3 py-1 rounded-full mb-4 uppercase tracking-widest shadow-sm">
                                    Sản Phẩm Nổi Bật
                                </span>
                                
                                <h1 class="text-2xl md:text-3xl font-extrabold mb-2 leading-tight drop-shadow-lg uppercase tracking-wide">
                                    {{ $product->tensp }}
                                </h1>
                                
                                <p class="text-xl md:text-2xl text-orange-200 font-bold mb-8 drop-shadow-md">
                                    {{ number_format($product->giaban ?? 0, 0, ',', '.') }} ₫
                                </p>
                                
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('product.show', $product->masp) }}" class="bg-[#FF6B35] hover:bg-orange-600 px-7 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all active:scale-95">
                                        Xem Chi Tiết
                                    </a>
                                    <a href="{{ route('cart.add', $product->masp) }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-orange-100 text-white px-7 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all active:scale-95">
                                        Thêm Vào Giỏ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="swiper-slide relative w-full h-full flex items-center bg-cover bg-center" style="background-image: url('{{ asset('images/bg-sunflower.jpg') }}');">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <div class="relative z-10 max-w-7xl mx-auto px-8 text-white">
                        <h1 class="text-3xl md:text-4xl font-extrabold mb-4">SunFlower</h1>
                        <p class="text-sm italic text-orange-100">Hoa Tươi Mỗi Ngày</p>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="swiper-button-next !text-white after:!text-xl w-12 h-12 bg-black/20 hover:bg-[#FF6B35] rounded-full backdrop-blur-sm transition-all hidden md:flex border orange-100 mr-4"></div>
        <div class="swiper-button-prev !text-white after:!text-xl w-12 h-12 bg-black/20 hover:bg-[#FF6B35] rounded-full backdrop-blur-sm transition-all hidden md:flex border orange-100 ml-4"></div>
        
        <div class="swiper-pagination !bottom-8"></div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".heroSwiper", {
            loop: true,                 // Trượt vòng lặp vô hạn
            grabCursor: true,           // Đổi con trỏ thành bàn tay để kéo thả
            effect: "slide",            // Hiệu ứng trượt (có thể đổi thành "fade" nếu muốn mờ dần)
            autoplay: {
                delay: 4000,            // Tự động trượt sau 4 giây
                disableOnInteraction: false, 
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,   // Hiệu ứng thu phóng cho dấu chấm
            },
        });
    });
</script>

    <section class="max-w-7xl mx-auto px-4">
        <div class="flex items-center justify-between mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900">Tất Cả Danh Mục</h2>
            <a href="{{ route('categories.index') }}" class="text-[#FF6B35] font-bold hover:underline">Xem tất cả →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @if(isset($categories) && $categories->count() > 0)
                @foreach($categories as $category)
                    <a href="{{ route('category.show', $category->madm) }}" class="relative h-56 rounded-3xl overflow-hidden group shadow-sm block border border-white">
                        
                        @php
                            $catImage = !empty($category->hinhanh) 
                                        ? route('category.image', $category->madm)
                                        : 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60';
                        @endphp
                        
                        <img src="{{ $catImage }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $category->tendm }}">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <h3 class="absolute bottom-6 left-6 text-white font-bold text-lg pr-4">{{ $category->tendm }}</h3>
                    </a>
                @endforeach
            @else
                <p class="text-gray-500 italic col-span-full">Chưa có danh mục nào.</p>
            @endif
        </div>
    </section>

    <section id="products" class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-extrabold text-gray-900 mb-4">Sản Phẩm Mới</h2>
            <div class="w-20 h-1.5 bg-[#FF6B35] mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @if(isset($products) && $products->count() > 0)
                @foreach($products as $product)
                    <div class="group bg-white border border-gray-100 rounded-3xl p-4 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-100/50 hover:-translate-y-1 relative">
                        <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-50 mb-5">
                            
                            @php
                                $prodImage = !empty($product->hinhanh)
                                             ? route('product.image', $product->masp)
                                             : asset('images/bg-sunflower.jpg');
                            @endphp

                            <img src="{{ asset('storage/' . ltrim($product->hinhanh, '/')) }}" 
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-110" 
                                 alt="{{ $product->tensp }}">
                            
                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        <div class="px-2">
                            <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-[#FF6B35] transition-colors line-clamp-1" title="{{ $product->tensp }}">
                                {{ $product->tensp }}
                            </h3>
                           <div class="mt-4 flex items-center justify-between">
                        <div>
                            @if(!empty($product->giakm) && $product->giakm < $product->giaban)
                                <span class="text-xl font-extrabold text-[#FF6B35]">
                                    {{ number_format($product->giakm, 0, ',', '.') }} ₫
                                </span>
                                <span class="text-xs text-gray-400 line-through ml-2">
                                    {{ number_format($product->giaban, 0, ',', '.') }} ₫
                                </span>
                            @else
                                <span class="text-xl font-extrabold text-[#FF6B35]">
                                    {{ number_format($product->giaban ?? 0, 0, ',', '.') }} ₫
                                </span>
                            @endif
                        </div>
                        <span class="bg-orange-50 text-[#FF6B35] text-xs font-bold px-2.5 py-1 rounded-lg">
                            Mới
                        </span>
                        </div>
                            
                        <div class="mt-6 pt-4 border-t border-gray-50 flex flex-col gap-3">
                            <div class="flex gap-2">
                                <a href="{{ route('product.show', $product->masp) }}" 
                                   class="flex-1 bg-gray-50 text-gray-600 text-center py-3 rounded-xl font-bold text-sm hover:bg-gray-100 transition">
                                    Chi tiết
                                </a>
                                <a href="{{ route('cart.add', $product->masp) }}" 
                                   class="w-12 h-12 bg-[#FF6B35] text-white flex items-center justify-center rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-100">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </a>
                                
                            </div>
                                <a href="{{ route('cart.buyNow', $product->masp) }}" 
                                   class="w-full h-11 bg-[#FF6B35] text-white flex items-center justify-center rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-lg shadow-orange-100 active:scale-95">
                                    Mua ngay
                                </a>
                            </div>    
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-10">
                    <p class="text-gray-500 italic">Hiện tại chưa có sản phẩm nào được bày bán.</p>
                </div>
            @endif

        </div> @if($products->hasPages())
        <div class="mt-8 flex justify-center w-full">
            {{ $products->appends(request()->query())->links('vendor.pagination.sunflower') }}
        </div>
        @endif
    </section>
</div>
@endsection