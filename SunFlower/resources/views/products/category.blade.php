@extends('layouts.app')

@section('title', ($category->tendm ?? 'Danh mục') . ' - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 lg:py-12 min-h-[70vh]">
    
    <nav class="text-sm text-gray-500 mb-8 flex items-center bg-gray-50 py-3 px-4 rounded-xl border border-gray-100 shadow-sm">
        <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition font-medium">Trang chủ</a>
        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <a href="{{ route('categories.index') }}" class="hover:text-[#FF6B35] transition font-medium">Danh mục</a>
        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="font-bold text-[#FF6B35] truncate">{{ $category->tendm ?? 'Tất cả sản phẩm' }}</span>
    </nav>

    <div class="mb-10">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2">{{ $category->tendm ?? 'Danh mục sản phẩm' }}</h1>
        <div class="w-20 h-1.5 bg-[#FF6B35] rounded-full"></div>
    </div>

    @if(isset($products) && $products->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $item)
                <a href="{{ route('product.show', $item->masp) }}" class="group bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-full">
                    
                    <div class="relative w-full aspect-square overflow-hidden bg-gray-50 p-4">
                        <img src="{{ str_starts_with($item->hinhanh, 'http') ? $item->hinhanh : asset('storage/' . ltrim($item->hinhanh, '/')) }}" 
                             alt="{{ $item->tensp }}" 
                             class="absolute inset-0 w-full h-full object-cover rounded-t-3xl group-hover:scale-110 transition-transform duration-700">
                        
                        <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                            <span class="bg-white text-gray-900 font-bold py-2 px-6 rounded-full shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                                Xem chi tiết
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow bg-white z-10 relative">
                        <h3 class="text-gray-900 font-bold mb-3 line-clamp-2 text-base group-hover:text-[#FF6B35] transition-colors leading-snug">
                            {{ $item->tensp }}
                        </h3>
                        
                        <div class="mt-auto">
                            @if(!empty($item->giakm) && $item->giakm < $item->giaban)
                                <div class="flex flex-col">
                                    <span class="text-[#FF6B35] font-extrabold text-xl">{{ number_format($item->giakm, 0, ',', '.') }} ₫</span>
                                    <span class="text-gray-400 line-through text-sm">{{ number_format($item->giaban, 0, ',', '.') }} ₫</span>
                                </div>
                            @else
                                <span class="text-[#FF6B35] font-extrabold text-xl">{{ number_format($item->giaban ?? 0, 0, ',', '.') }} ₫</span>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-24 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">Chưa có sản phẩm</h3>
            <p class="text-gray-500 mb-6">Hiện tại danh mục này chưa được cập nhật hoa mới.</p>
            <a href="{{ route('home') }}" class="inline-block bg-white border-2 border-[#FF6B35] text-[#FF6B35] font-bold px-6 py-2.5 rounded-xl hover:bg-orange-50 transition">
                Quay lại Trang chủ
            </a>
        </div>
    @endif
</div>
@endsection