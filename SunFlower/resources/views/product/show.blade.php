@extends('layouts.app')

@section('title', ($product->tensp ?? 'Chi tiết hoa') . ' - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[70vh]">
    @if(isset($product) && $product)
        <nav class="text-sm text-gray-500 mb-8 flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('categories.index') }}" class="hover:text-[#FF6B35] transition">Danh mục</a>
            <span class="mx-2">/</span>
            <span class="font-bold text-gray-800 truncate">{{ $product->tensp ?? 'Chi tiết hoa' }}</span>
        </nav>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                
                <div class="bg-gray-50 p-8 flex items-center justify-center border-r border-gray-100 relative group">
                    <img src="{{ asset('storage/image/' . ($product->hinhanh ?? 'images/default-flower.jpg')) }}" 
                         alt="{{ $product->tensp ?? 'Hoa' }}"
                         class="w-full max-w-md h-auto rounded-2xl shadow-sm object-cover aspect-square group-hover:scale-105 transition duration-500">
                    
                    <div class="absolute top-6 left-6 bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md uppercase tracking-wider">
                        Hot Trend
                    </div>
                </div>

                <div class="p-8 md:p-12 flex flex-col justify-center">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4">{{ $product->tensp ?? 'Tên sản phẩm' }}</h1>
                    
                    <div class="flex items-end gap-4 mb-6">
                        @if(!empty($product->giakm) && $product->giakm < $product->giaban)
                            <span class="text-4xl font-extrabold text-[#FF6B35]">
                                {{ number_format($product->giakm, 0, ',', '.') }} ₫
                            </span>
                            <span class="text-gray-400 line-through text-lg">
                                {{ number_format($product->giaban, 0, ',', '.') }} ₫
                            </span>
                            <span class="bg-red-100 text-red-600 px-2 py-1 rounded-md text-sm font-bold mb-1">
                                -{{ round((($product->giaban - $product->giakm) / $product->giaban) * 100) }}%
                            </span>
                        @else
                            <span class="text-4xl font-extrabold text-[#FF6B35]">
                                {{ number_format($product->giaban ?? 0, 0, ',', '.') }} ₫
                            </span>
                        @endif
                    </div>

                    <div class="text-gray-600 mb-8 leading-relaxed">
                        <p>{{ $product->mota ?? 'Một bó hoa tươi thắm thay cho vạn lời muốn nói. Thiết kế độc quyền tại SunFlower.' }}</p>
                    </div>

                    <hr class="border-gray-100 mb-8">

                    <div class="flex gap-4">
                        <a href="{{ route('cart.add', $product->masp) }}" 
                           class="flex-1 bg-[#FF6B35] hover:bg-orange-600 text-white text-center py-4 rounded-xl font-bold text-lg shadow-lg shadow-orange-200 transition duration-300 flex items-center justify-center gap-3 active:scale-95">
                            Thêm Vào Giỏ Hàng
                        </a>
                    </div>
                    
                    </div>
            </div>
        </div>
    @else
        <div class="text-center py-32 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Sản phẩm không tồn tại!</h2>
            <a href="{{ route('home') }}" class="inline-block bg-[#FF6B35] text-white px-8 py-3.5 rounded-xl">
                Về Trang Chủ
            </a>
        </div>
    @endif
</div>
@endsection