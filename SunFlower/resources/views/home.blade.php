@extends('layouts.app')

@section('title', 'Trang chủ - SunFlower')

@section('content')
<div class="bg-gray-50 space-y-20 pb-20">
    
    <section class="relative w-full min-h-[600px] flex items-center bg-cover bg-center"
             style="background-image: url('{{ $heroImage ?? asset('images/bg-sunflower.jpg') }}');"> <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 text-white">
            <span class="inline-block bg-[#FF6B35] text-white text-xs font-bold px-4 py-1.5 rounded-full mb-6 uppercase tracking-widest">Premium Collection</span>
            <h1 class="text-6xl md:text-7xl font-extrabold mb-6 leading-tight">SunFlower<br><span class="text-orange-100">Hoa Tươi Mỗi Ngày</span></h1>
            <p class="text-lg text-gray-100 mb-10 max-w-xl italic">Gói trọn yêu thương vào từng đóa hoa tươi thắm nhất, giao tận tay người thương chỉ trong 2 giờ.</p>
            <a href="#products" class="bg-[#FF6B35] hover:bg-orange-600 px-10 py-4 rounded-xl font-bold text-lg shadow-lg shadow-orange-900/20 transition-all active:scale-95 inline-block">Mua Ngay</a>
        </div>
    </section>

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
                                        ? asset('storage/' . $category->hinhanh) 
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
                                             ? asset('storage/' . $product->hinhanh) 
                                             : asset('images/bg-sunflower.jpg');
                            @endphp

                            <img src="{{ $prodImage }}" 
                                 class="w-full h-full object-cover transition duration-500 group-hover:scale-110" 
                                 alt="{{ $product->tensp }}">
                            
                            <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>

                        <div class="px-2">
                            <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-[#FF6B35] transition-colors line-clamp-1" title="{{ $product->tensp }}">
                                {{ $product->tensp }}
                            </h3>
                            <div class="flex items-center gap-3 mb-5">
                                <span class="text-[#FF6B35] font-black text-xl">
                                    {{ number_format($product->giaban, 0, ',', '.') }} đ
                                </span>
                                <span class="text-gray-300 line-through text-xs italic">
                                    {{ number_format($product->giaban * 1.2, 0, ',', '.') }} đ
                                </span>
                            </div>
                            
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
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-10">
                    <p class="text-gray-500 italic">Hiện tại chưa có sản phẩm nào được bày bán.</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection