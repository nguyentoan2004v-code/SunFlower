@extends('layouts.app')

@section('title', 'Hoa theo danh mục - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[60vh]">
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#FF6B35]">Trang chủ</a>
        <span class="mx-2">/</span>
        <a href="{{ route('categories.index') }}" class="hover:text-[#FF6B35]">Danh mục</a>
        <span class="mx-2">/</span>
        <span class="font-bold text-gray-800">Sản phẩm</span>
    </nav>

    <h2 class="text-3xl font-bold mb-10 text-gray-800 border-b pb-4">
        Hoa Trong Danh Mục
    </h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @if(isset($categoryProducts) && count($categoryProducts) > 0)
            @foreach($categoryProducts as $product)
                @if(is_array($product) && isset($product['masp']))
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col group shadow-sm hover:shadow-lg transition duration-300">
                        <a href="{{ route('product.show', $product['masp']) }}" class="aspect-square overflow-hidden rounded-xl mb-4 relative">
                            <img src="{{ asset('storage/' . ($product['hinhanh'] ?? '')) }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <span class="bg-white text-gray-800 px-4 py-2 rounded-full font-semibold text-sm shadow-md">Xem chi tiết</span>
                            </div>
                        </a>
                        <h3 class="font-bold text-gray-800 text-center mb-2 line-clamp-1 hover:text-[#FF6B35] transition">
                            <a href="{{ route('product.show', $product['masp']) }}">{{ $product['tensp'] ?? 'Tên hoa' }}</a>
                        </h3>
                        <p class="text-[#FF6B35] font-black text-center text-xl mb-4">
                            {{ number_format($product['giaban'] ?? 0, 0, ',', '.') }} đ
                        </p>
                        <a href="{{ route('cart.add', $product['masp']) }}" 
                           class="mt-auto w-full bg-gray-50 text-[#FF6B35] border border-[#FF6B35] hover:bg-[#FF6B35] hover:text-white text-center py-2.5 rounded-xl font-bold transition">
                            Thêm vào giỏ
                        </a>
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-span-full text-center py-16 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                <p class="text-gray-500 text-lg mb-4">Hiện tại chưa có sản phẩm nào trong danh mục này.</p>
                <a href="{{ route('home') }}" class="text-[#FF6B35] font-bold hover:underline">Về trang chủ</a>
            </div>
        @endif
    </div>
</div>
@endsection