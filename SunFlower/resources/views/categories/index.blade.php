@extends('layouts.app')

@section('title', 'Danh mục & Sản phẩm - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[60vh]">
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Trang chủ</a>
        <span class="mx-2">/</span>
        <span class="font-bold text-gray-800">Cửa hàng</span>
    </nav>

    <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">Chọn Hoa Theo Chủ Đề</h2>

    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-20">
        @if(isset($categories) && $categories->count() > 0)
            @foreach($categories as $category)
                <a href="{{ route('category.show', $category->madm) }}" class="relative h-56 rounded-2xl overflow-hidden group shadow-sm hover:shadow-md transition block border border-gray-100">
                    <img src="{{ asset('storage/image/' . ($category->hinhanh ?? 'images/default-flower.jpg')) }}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition duration-500" alt="{{ $category->tendm ?? 'Danh mục' }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-center">
                        <h3 class="text-white font-bold text-lg drop-shadow-md">{{ $category->tendm ?? 'Danh mục' }}</h3>
                    </div>
                </a>
            @endforeach
        @else
            <p class="col-span-full text-center text-gray-500 py-10">Chưa có danh mục nào được cập nhật.</p>
        @endif
    </div>

    <div class="flex items-center justify-between mb-8 border-b pb-4">
        <h2 class="text-3xl font-bold text-gray-800">Tất Cả Hoa</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @if(isset($products) && $products->count() > 0)
            @foreach($products as $product)
                <div class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col group shadow-sm hover:shadow-lg transition duration-300">
                    <a href="{{ route('product.show', $product->masp) }}" class="aspect-square overflow-hidden rounded-xl mb-4 relative">
                        <img src="{{ asset('storage/image/' . ($product->hinhanh ?? '')) }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <span class="bg-white text-gray-800 px-4 py-2 rounded-full font-semibold text-sm shadow-md">Xem chi tiết</span>
                        </div>
                    </a>
                    <h3 class="font-bold text-gray-800 text-center mb-2 line-clamp-1 hover:text-[#FF6B35] transition">
                        <a href="{{ route('product.show', $product->masp) }}">{{ $product->tensp ?? 'Tên hoa' }}</a>
                    </h3>
                    <p class="text-[#FF6B35] font-black text-center text-xl mb-4">
                        {{ number_format($product->giaban ?? 0, 0, ',', '.') }} đ
                    </p>
                    <a href="{{ route('cart.add', $product->masp) }}" 
                       class="mt-auto w-full bg-gray-50 text-[#FF6B35] border border-[#FF6B35] hover:bg-[#FF6B35] hover:text-white text-center py-2.5 rounded-xl font-bold transition">
                        Thêm vào giỏ
                    </a>
                </div>
            @endforeach
        @else
            <div class="col-span-full text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-lg mb-4">Hiện tại chưa có sản phẩm nào.</p>
            </div>
        @endif
    </div>
</div>
@endsection