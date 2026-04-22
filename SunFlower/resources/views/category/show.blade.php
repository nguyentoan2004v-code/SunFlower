@extends('layouts.app')

@section('title', 'Danh mục sản phẩm - SunFlower')

@section('content')
<div class="bg-gray-50 min-h-screen pb-20">
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <nav class="flex mb-4 text-sm text-gray-400 font-medium">
                <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Trang chủ</a>
                <span class="mx-2">/</span>
                <span class="text-gray-600">Danh mục</span>
            </nav>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">
                Khám Phá <span class="text-[#FF6B35]">Sắc Hoa</span>
            </h1>
            <p class="mt-3 text-gray-500 max-w-2xl">
                Tất cả những đóa hoa tươi thắm nhất được chúng tôi tuyển chọn kỹ lưỡng mỗi ngày để gửi gắm trọn vẹn yêu thương.
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 mt-10">
        <div class="flex flex-col md:flex-row gap-10">
            
            <aside class="w-full md:w-64 flex-shrink-0">
                <div class="sticky top-28 space-y-8">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest mb-5">Danh Mục Hoa</h3>
                        <div class="flex flex-col gap-2">
                            @foreach($categories as $cat)
                                <a href="{{ route('category.show', $cat->madm) }}" 
                                   class="group flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ request()->route('madm') == $cat->madm ? 'bg-[#FF6B35] text-white shadow-lg shadow-orange-100' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-[#FF6B35] border border-transparent hover:border-orange-100' }}">
                                    <span class="font-semibold text-sm">{{ $cat->tendm }}</span>
                                    <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#FEF9E7] p-6 border border-orange-100">
                        <h4 class="font-bold text-[#FF6B35] mb-2">Ưu đãi hôm nay</h4>
                        <p class="text-xs text-gray-600 leading-relaxed">Giảm ngay 10% cho đơn hàng đầu tiên của khách hàng mới.</p>
                    </div>
                </div>
            </aside>

            <main class="flex-1">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-gray-900">
                        @php 
                            $currentCat = $categories->where('madm', request()->route('madm'))->first();
                        @endphp
                        {{ $currentCat ? $currentCat->tendm : 'Tất cả sản phẩm' }} 
                        <span class="text-sm font-normal text-gray-400 ml-2">({{ $categoryProducts->count() }} sản phẩm)</span>
                    </h2>
                </div>

                @if($categoryProducts->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($categoryProducts as $product)
                            <div class="group bg-white border border-gray-100 rounded-3xl p-4 transition-all duration-300 hover:shadow-2xl hover:shadow-orange-100/50 hover:-translate-y-1">
                                <div class="relative aspect-square overflow-hidden rounded-2xl bg-gray-50 mb-5">
                                    <img src="{{ route('product.image', $product->masp) }}"
                                         class="w-full h-full object-cover transition duration-500 group-hover:scale-110" 
                                         alt="{{ $product->tensp }}">
                                    
                                    <div class="absolute inset-0 bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>

                                <div class="px-2">
                                    <h3 class="font-bold text-gray-800 text-lg mb-1 group-hover:text-[#FF6B35] transition-colors line-clamp-1">
                                        {{ $product->tensp }}
                                    </h3>
                                    <div class="flex items-center gap-3 mb-5">
                                            @if(!empty($product->giakm) && $product->giakm < $product->giaban)
                                                <span class="text-[#FF6B35] font-black text-xl">
                                                    {{ number_format($product->giakm, 0, ',', '.') }} ₫
                                                </span>
                                                <span class="text-gray-400 line-through text-xs italic">
                                                    {{ number_format($product->giaban, 0, ',', '.') }} ₫
                                                </span>
                                            @else
                                                <span class="text-[#FF6B35] font-black text-xl">
                                                    {{ number_format($product->giaban ?? 0, 0, ',', '.') }} ₫
                                                </span>
                                            @endif
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
                    </div>
                @else
                    <div class="bg-white rounded-3xl border border-dashed border-gray-200 py-24 text-center">
                        <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-4xl">🌸</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Danh mục này đang đợi hoa về...</h3>
                        <p class="text-gray-500 mb-8">Đừng lo, chúng mình vẫn còn rất nhiều đóa hoa xinh tươi ở các danh mục khác!</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-[#FF6B35] text-white px-8 py-3 rounded-xl font-bold hover:bg-orange-600 transition">
                            Tiếp tục xem hoa
                        </a>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection