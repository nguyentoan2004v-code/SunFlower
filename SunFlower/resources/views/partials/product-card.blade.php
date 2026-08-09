{{--
    Partial: Thẻ sản phẩm dùng chung cho tất cả các trang.
    Style đồng bộ theo trang chủ (home.blade.php).
    
    Truyền vào: $product (model SanPham)
--}}
@php
    $gridImage = asset('images/bg-sunflower.jpg');
    if (!empty($product->hinhanh)) {
        $gridImage = str_starts_with($product->hinhanh, 'http')
                    ? $product->hinhanh
                    : asset('storage/' . ltrim($product->hinhanh, '/'));
    }
    $onSale = !empty($product->giakm) && $product->giakm < $product->giaban;
@endphp

<div class="group relative">
    <div class="relative aspect-square overflow-hidden bg-gray-100">

        {{-- Toàn bộ ảnh dẫn đến trang chi tiết --}}
        <a href="{{ route('product.show', $product->masp) }}" class="absolute inset-0 z-0" aria-label="Xem chi tiết {{ $product->tensp }}"></a>

        <img src="{{ $gridImage }}"
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105 pointer-events-none"
             alt="{{ $product->tensp }}">

        @if($onSale)
            <span class="absolute top-3 left-3 z-10 bg-[#FF6B35] text-white text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-full pointer-events-none">
                Giảm giá
            </span>
        @endif

        {{-- Gradient mờ nhẹ phía dưới khi hover --}}
        <div class="absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-black/40 via-black/5 to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100 pointer-events-none"></div>

        {{-- 2 nút hành động khi hover --}}
        <div class="absolute inset-x-0 bottom-4 z-10 flex items-center justify-center gap-2 px-4 opacity-0 translate-y-2 transition-all duration-500 ease-out group-hover:opacity-100 group-hover:translate-y-0">
            <form action="{{ route('cart.add', $product->masp) }}" method="POST">
                @csrf
                <button type="submit" title="Thêm vào giỏ hàng"
                        class="w-10 h-10 rounded-full bg-white/95 backdrop-blur-sm shadow-lg shadow-black/10 flex items-center justify-center text-gray-700 transition-colors duration-300 hover:bg-[#FF6B35] hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </button>
            </form>
            <a href="{{ route('cart.buyNow', $product->masp) }}"
               class="h-10 px-5 rounded-full bg-white/95 backdrop-blur-sm shadow-lg shadow-black/10 flex items-center justify-center text-gray-800 text-xs font-bold transition-colors duration-300 hover:bg-[#FF6B35] hover:text-white">
                Mua ngay
            </a>
        </div>
    </div>

    <a href="{{ route('product.show', $product->masp) }}" class="mt-4 block">
        <h3 class="text-gray-900 text-base font-semibold leading-snug line-clamp-1 group-hover:text-[#FF6B35] transition-colors" title="{{ $product->tensp }}">
            {{ $product->tensp }}
        </h3>
        <div class="mt-2 flex items-baseline gap-2">
            @if($onSale)
                <span class="font-['Fraunces'] text-xl font-semibold text-[#FF6B35]">
                    {{ number_format($product->giakm, 0, ',', '.') }} ₫
                </span>
                <span class="text-sm text-gray-400 line-through">
                    {{ number_format($product->giaban, 0, ',', '.') }} ₫
                </span>
            @else
                <span class="font-['Fraunces'] text-xl font-semibold text-gray-900">
                    {{ number_format($product->giaban ?? 0, 0, ',', '.') }} ₫
                </span>
            @endif
        </div>
    </a>
</div>
