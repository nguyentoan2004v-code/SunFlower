@extends('layouts.app')

@section('title', 'Trang chủ - SunFlower')

@section('content')
<div class="bg-gray-50 space-y-20 pb-20">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<section class="relative w-full max-w-7xl mx-auto px-4 mt-8 h-[250px] sm:h-[350px] lg:h-[500px]">
    
    <!-- KHÔNG CÒN rounded-[32px] và border, Banner sẽ vuông vức và tràn lề -->
    <div class="swiper heroSwiper w-full h-full overflow-hidden shadow-xl group">
        <div class="swiper-wrapper">
            
            <!-- ẢNH BANNER 1 -->
            <div class="swiper-slide w-full h-full overflow-hidden">
                <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780499734/Banner2_lbblsf.jpg" 
                     class="w-full h-full object-cover transform transition-transform duration-[3000ms] ease-out group-hover:scale-105" 
                     alt="Banner SunFlower 1">
            </div>

            <!-- ẢNH BANNER 2 -->
            <div class="swiper-slide w-full h-full overflow-hidden">
                <img src="https://res.cloudinary.com/drgrh0yeo/image/upload/v1780499543/banner1_j2prs3.jpg" 
                     class="w-full h-full object-cover transform transition-transform duration-[3000ms] ease-out group-hover:scale-105" 
                     alt="Banner SunFlower 2">
            </div>

        </div>
        
        <!-- VÙNG TÀNG HÌNH CHUYỂN SLIDE -->
        <div id="hover-prev" class="absolute top-0 left-0 w-[10%] h-full z-10 cursor-pointer bg-gradient-to-r from-black/5 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></div>
        
        <div id="hover-next" class="absolute top-0 right-0 w-[10%] h-full z-10 cursor-pointer bg-gradient-to-l from-black/5 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-500"></div>
        
        <!-- Dấu chấm tròn ở dưới -->
        <div class="swiper-pagination !bottom-5"></div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".heroSwiper", {
            loop: true,
            grabCursor: true,
            effect: "slide",
            autoplay: {
                delay: 4000,
                disableOnInteraction: false, 
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },
        });
        document.getElementById('hover-next').addEventListener('mouseenter', function() {
            swiper.slideNext();
        });

        document.getElementById('hover-prev').addEventListener('mouseenter', function() {
            swiper.slidePrev();
        });
    });
</script>

    <section class="max-w-7xl mx-auto px-4">
        <div class="flex items-end justify-between mb-8">
            <div>
                <span class="text-xs font-semibold tracking-[0.2em] text-[#FF6B35] uppercase">Khám phá</span>
                <h2 class="font-['Fraunces'] text-3xl md:text-4xl font-medium text-gray-900 mt-1">Danh mục hoa</h2>
            </div>
            <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-gray-500 hover:text-[#FF6B35] transition-colors flex items-center gap-1 shrink-0">
                Xem tất cả <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="grid grid-cols-2 gap-4 md:gap-5 lg:grid-cols-4 lg:auto-rows-[180px]">
            @if(isset($categories) && $categories->count() > 0)
                @foreach($categories->take(5) as $i => $category)
                    @php
                        // SỬA LỖI 2: Ảnh Danh Mục
                        $catImage = 'https://images.unsplash.com/photo-1563241527-3004b7be0ffd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=60'; // Mặc định
                        if (!empty($category->hinhanh)) {
                            $catImage = str_starts_with($category->hinhanh, 'http')
                                        ? $category->hinhanh
                                        : asset('storage/' . ltrim($category->hinhanh, '/'));
                        }
                        // Danh mục đầu tiên được ưu tiên hiển thị lớn hơn để tạo điểm nhấn thị giác,
                        // thay vì chia đều 5 ô bằng nhau khiến bố cục bị chật và đơn điệu.
                        $isFeatured = $i === 0;
                    @endphp
                    <a href="{{ route('category.show', $category->madm) }}"
                       class="group relative block overflow-hidden rounded-2xl bg-gray-100
                              {{ $isFeatured
                                    ? 'col-span-2 aspect-[16/10] lg:aspect-auto lg:row-span-2'
                                    : 'aspect-[4/5] lg:aspect-auto' }}">

                        <img src="{{ $catImage }}"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"
                             alt="{{ $category->tendm }}">

                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/5 to-transparent"></div>

                        <div class="absolute inset-x-0 bottom-0 p-4 md:p-5 {{ $isFeatured ? 'lg:p-7' : '' }}">
                            <h3 class="font-['Fraunces'] text-white font-medium leading-tight {{ $isFeatured ? 'text-xl md:text-2xl' : 'text-base md:text-lg' }}">
                                {{ $category->tendm }}
                            </h3>
                            <span class="inline-block mt-2 h-[2px] w-6 bg-[#FF6B35] transition-all duration-300 group-hover:w-10"></span>
                        </div>
                    </a>
                @endforeach
            @else
                <p class="text-gray-500 italic col-span-full">Chưa có danh mục nào.</p>
            @endif
        </div>
    </section>

    <section id="products" class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12">
            <span class="text-xs font-semibold tracking-[0.2em] text-[#FF6B35] uppercase">Vừa về cửa hàng</span>
            <h2 class="font-['Fraunces'] text-3xl md:text-4xl font-medium text-gray-900 mt-2">Sản phẩm mới</h2>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-10 md:gap-x-6">
            @if(isset($products) && $products->count() > 0)
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product])
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