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
                    <img src="{{ route('product.image', $product->masp) }}" 
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
                        @php
                            $description = $product->mota ?? "Một bó hoa tươi thắm thay cho vạn lời muốn nói.\nSản phẩm bao gồm:\n- Hoa hồng đỏ\n- Giấy gói cao cấp";
                            $lines = preg_split('/\\r\\n|\\r|\\n/', $description);
                            $showIcons = false; // Flag để kiểm soát việc hiển thị icon
                        @endphp

                        @foreach($lines as $line)
                            @if(trim($line) === '') @continue @endif

                            {{-- Kiểm tra xem dòng hiện tại có phải là dòng kích hoạt không --}}
                            @if(stripos(trim($line), 'sản phẩm bao gồm') !== false)
                                @php $showIcons = true; @endphp
                                {{-- Hiển thị dòng kích hoạt không có icon, có thể in đậm --}}
                                <p class="font-bold text-gray-800 mt-4 mb-3">{{ trim($line) }}</p>
                            @elseif($showIcons)
                                {{-- Nếu flag là true, hiển thị dòng với icon --}}
                                <div class="flex items-start gap-3 mb-2">
                                    <svg class="w-4 h-4 text-orange-400 mt-1 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10.75 1.25a.75.75 0 00-1.5 0v2.55a1 1 0 01-1.485.861l-2.22-1.282a.75.75 0 00-1.061 1.061l1.282 2.22a1 1 0 01-.861 1.485H2.75a.75.75 0 000 1.5h2.55a1 1 0 01.861 1.485l-1.282 2.22a.75.75 0 101.061 1.061l2.22-1.282a1 1 0 011.485.861v2.55a.75.75 0 001.5 0v-2.55a1 1 0 011.485-.861l2.22 1.282a.75.75 0 101.061-1.061l-1.282-2.22a1 1 0 01.861-1.485h2.55a.75.75 0 000-1.5h-2.55a1 1 0 01-.861-1.485l1.282-2.22a.75.75 0 00-1.061-1.061l-2.22 1.282a1 1 0 01-1.485-.861V1.25z" />
                                    </svg>
                                    <span>{{ trim($line) }}</span>
                                </div>
                            @else
                                {{-- Nếu flag là false, hiển thị dòng không có icon --}}
                                <p class="mb-2">{{ trim($line) }}</p>
                            @endif
                        @endforeach
                    </div>

                    <hr class="border-gray-100 mb-6">
                    
                    <!-- Phần chọn số lượng -->
                    <div class="mb-8 flex items-center gap-6">
                        <span class="text-gray-700 font-bold text-lg">Số lượng:</span>
                        <div class="flex items-center justify-center border border-gray-200 rounded-xl w-32 overflow-hidden bg-white shadow-sm">
                            <button type="button" class="px-4 py-2 hover:bg-gray-100 btn-minus-qty transition text-gray-600 font-bold text-lg">-</button>
                            <input type="text" id="product-quantity" class="w-12 text-center border-none focus:ring-0 font-bold text-gray-700 text-lg p-0" value="1" readonly>
                            <button type="button" class="px-4 py-2 hover:bg-gray-100 btn-plus-qty transition text-gray-600 font-bold text-lg">+</button>
                        </div>
                    </div>

                <div class="flex gap-4">
                    <a href="{{ route('cart.add', $product->masp) }}" id="btn-add-cart"
                       class="w-1/3 bg-white border-2 border-[#FF6B35] text-[#FF6B35] flex items-center justify-center gap-2 py-4 rounded-2xl font-bold text-base hover:bg-orange-50 transition duration-300 active:scale-95 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Thêm<span class="hidden sm:inline">Giỏ hàng</span>
                    </a>

                    <a href="{{ route('cart.buyNow', $product->masp) }}" id="btn-buy-now"
                       class="flex-1 bg-[#FF6B35] text-white flex items-center justify-center rounded-2xl font-bold text-lg hover:bg-orange-600 transition duration-300 shadow-lg shadow-orange-100 active:scale-95">
                        Mua ngay
                    </a>
                </div>              
                    
                    

                        <p>{{ $product->mota ?? 'Một bó hoa tươi thắm thay cho vạn lời muốn nói. Thiết kế độc quyền tại SunFlower.' }}</p>
                    </div>

                    <hr class="border-gray-100 mb-8">
                <div class="flex gap-4 mt-8">
                    <a href="{{ route('cart.add', $product->masp) }}" 
                       class="w-1/3 bg-white border-2 border-[#FF6B35] text-[#FF6B35] flex items-center justify-center gap-2 py-4 rounded-2xl font-bold text-base hover:bg-orange-50 transition duration-300 active:scale-95 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Thêm<span class="hidden sm:inline">Giỏ hàng</span>
                    </a>

                    <a href="{{ route('cart.buyNow', $product->masp) }}" 
                       class="flex-1 bg-[#FF6B35] text-white flex items-center justify-center rounded-2xl font-bold text-lg hover:bg-orange-600 transition duration-300 shadow-lg shadow-orange-100 active:scale-95">
                        Mua ngay
                    </a>
                </div>              
                    
                    
                </div>
            </div>
        </div>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const inputQty = document.getElementById('product-quantity');
                const btnMinus = document.querySelector('.btn-minus-qty');
                const btnPlus = document.querySelector('.btn-plus-qty');
                const btnAddCart = document.getElementById('btn-add-cart');
                const btnBuyNow = document.getElementById('btn-buy-now');

                if (!inputQty || !btnMinus || !btnPlus) return;

                const baseAddCartUrl = "{{ route('cart.add', $product->masp) }}";
                const baseBuyNowUrl = "{{ route('cart.buyNow', $product->masp) }}";

                function updateLinks() {
                    const qty = inputQty.value;
                    if(btnAddCart) btnAddCart.href = baseAddCartUrl + "?quantity=" + qty;
                    if(btnBuyNow) btnBuyNow.href = baseBuyNowUrl + "?quantity=" + qty;
                }

                btnMinus.addEventListener('click', () => {
                    let qty = parseInt(inputQty.value);
                    if (qty > 1) {
                        inputQty.value = qty - 1;
                        updateLinks();
                    }
                });

                btnPlus.addEventListener('click', () => {
                    let qty = parseInt(inputQty.value);
                    inputQty.value = qty + 1;
                    updateLinks();
                });

                updateLinks();
            });
        </script>
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