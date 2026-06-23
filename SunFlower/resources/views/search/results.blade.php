@extends('layouts.app')

@section('title', 'Tìm kiếm: ' . $keyword)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[60vh]">
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#FF6B35]">Trang chủ</a> / <span class="font-bold text-gray-800">Tìm kiếm</span>
    </nav>

    <div class="mb-10 border-b pb-4">
        <div class="flex items-center flex-wrap gap-3 mb-2">
            <h2 class="text-2xl">Kết quả cho: <span class="font-bold text-[#FF6B35]">"{{ $keyword }}"</span></h2>

            {{-- Badge hiển thị chế độ tìm kiếm --}}
            @if(isset($searchType) && $searchType === 'semantic')
                <span class="inline-flex items-center gap-1 bg-gradient-to-r from-purple-500 to-indigo-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    AI Semantic Search
                </span>
            @else
                <span class="inline-flex items-center gap-1 bg-gray-200 text-gray-600 text-xs font-bold px-3 py-1 rounded-full">
                    Tìm kiếm thường
                </span>
            @endif
        </div>
        <p class="text-gray-500 mt-1">Tìm thấy {{ $products->total() }} đóa hoa phù hợp.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @if(!empty($products) && count($products) > 0)
            @foreach($products as $product)
                <div class="group bg-white border border-gray-100 rounded-3xl p-4 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-100/50 hover:-translate-y-1 relative">
                    <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-50 mb-5">
                        @php
                            $prodImage = asset('images/bg-sunflower.jpg');
                            if (!empty($product->hinhanh)) {
                                $prodImage = str_starts_with($product->hinhanh, 'http') 
                                            ? $product->hinhanh 
                                            : asset('storage/' . ltrim($product->hinhanh, '/'));
                            }
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
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-50 flex flex-col gap-3">
                            <div class="flex gap-2">
                                <a href="{{ route('product.show', $product->masp) }}" 
                                   class="flex-1 bg-gray-50 text-gray-600 text-center py-3 rounded-xl font-bold text-sm hover:bg-gray-100 transition">
                                    Chi tiết
                                </a>
                                <form action="{{ route('cart.add', $product->masp) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-12 h-12 bg-[#FF6B35] text-white flex items-center justify-center rounded-xl hover:bg-orange-600 transition shadow-lg shadow-orange-100">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    </button>
                                </form>
                            </div>
                            <a href="{{ route('cart.buyNow', $product->masp) }}" 
                               class="w-full h-11 bg-[#FF6B35] text-white flex items-center justify-center rounded-xl font-bold text-sm hover:bg-orange-600 transition shadow-lg shadow-orange-100 active:scale-95">
                                Mua ngay
                            </a>
                        </div>    
                    </div>
                </div>
            @endforeach
            <div class="col-span-full mt-8 flex justify-center">
                {{ $products->withQueryString()->links() }}
            </div>
        @else
            <div class="col-span-full text-center py-20 bg-gray-50 rounded-3xl border border-dashed">
                <div class="text-5xl mb-4">🥀</div>
                <p class="text-gray-700 text-lg font-bold mb-2">Không tìm thấy bông hoa nào!</p>
                <p class="text-gray-400 text-sm mb-6">
                    @if(isset($searchType) && $searchType === 'semantic')
                        AI đã tìm khắp vườn hoa nhưng không có loài hoa nào phù hợp với <strong>"{{ $keyword }}"</strong>
                    @else
                        Không có sản phẩm nào tên <strong>"{{ $keyword }}"</strong>
                    @endif
                </p>
                <a href="{{ route('home') }}" class="inline-block bg-[#FF6B35] text-white px-6 py-3 rounded-xl font-bold hover:bg-orange-600 transition">
                    Quay về trang chủ
                </a>
            </div>
        @endif
    </div>
</div>
@endsection