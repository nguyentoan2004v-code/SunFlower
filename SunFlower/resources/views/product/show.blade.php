@extends('layouts.app')

@section('title', ($product->tensp ?? 'Chi tiết hoa') . ' - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[70vh]">
    @if(isset($product) && $product)
        <nav class="text-sm text-gray-500 mb-8 flex items-center">
            <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition font-medium">Trang chủ</a>
            <span class="mx-3 text-gray-300">/</span>
            <a href="{{ route('categories.index') }}" class="hover:text-[#FF6B35] transition font-medium">Danh mục</a>
            <span class="mx-3 text-gray-300">/</span>
            <span class="font-bold text-[#FF6B35] truncate">{{ $product->tensp ?? 'Chi tiết hoa' }}</span>
        </nav>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-12">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-0">
                
                <div class="md:col-span-5 bg-gray-50 p-8 flex items-center justify-center relative group">
                    <img src="{{ str_starts_with($product->hinhanh, 'http') ? $product->hinhanh : asset('storage/' . ltrim($product->hinhanh, '/')) }}" 
                        alt="{{ $product->tensp ?? 'Hoa' }}"
                        class="w-full h-auto object-cover aspect-square rounded-2xl shadow-sm group-hover:scale-105 transition duration-700">
                    
                    <div class="absolute top-6 left-6 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-md uppercase tracking-wider">
                        Hot Trend
                    </div>
                </div>

                <div class="md:col-span-7 p-8 md:p-12 flex flex-col justify-center border-l border-gray-50">
                    
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-3 leading-tight">{{ $product->tensp ?? 'Tên sản phẩm' }}</h1>
                    
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div class="flex items-center text-yellow-400 text-sm">
                            {{-- IN SAO ĐỘNG DỰA TRÊN ĐIỂM TRUNG BÌNH --}}
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                    <svg class="w-4 h-4 fill-current text-yellow-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                            <a href="#danh-gia-khach-hang" class="text-gray-500 hover:text-[#FF6B35] font-medium ml-2 font-sans transition underline decoration-dashed underline-offset-4">
                                ({{ $totalReviews }} đánh giá)
                            </a>
                        </div>
                        <div class="h-4 w-px bg-gray-300"></div>
                        <span class="text-sm text-gray-500">Mã SP: <strong class="text-gray-800">{{ $product->masp }}</strong></span>
                        <div class="h-4 w-px bg-gray-300 hidden sm:block"></div>
                        @php
                            $tonKho = $product->lohangs_sum_soluong_ton ?? 0;
                        @endphp
                        @if($tonKho > 0)
                            <span class="text-sm text-green-600 font-medium hidden sm:block">
                                <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span> Còn hàng (Trong kho còn {{ $tonKho }} đóa)
                            </span>
                        @else
                            <span class="text-sm text-red-600 font-medium hidden sm:block">
                                <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-1"></span> Hết hàng
                            </span>
                        @endif
                    </div>
                    
                    <div class="bg-orange-50/50 rounded-2xl p-4 sm:p-6 mb-8 border border-orange-100 flex flex-wrap items-end gap-4">
                        @if(!empty($product->giakm) && $product->giakm < $product->giaban)
                            <span class="text-4xl font-black text-[#FF6B35] tracking-tight">
                                {{ number_format($product->giakm, 0, ',', '.') }} ₫
                            </span>
                            <span class="text-gray-400 line-through text-lg mb-1 font-medium">
                                {{ number_format($product->giaban, 0, ',', '.') }} ₫
                            </span>
                            <span class="bg-gradient-to-r from-red-500 to-rose-500 text-white px-3 py-1 rounded-lg text-sm font-bold mb-1 shadow-sm">
                                Tiết kiệm {{ round((($product->giaban - $product->giakm) / $product->giaban) * 100) }}%
                            </span>
                        @else
                            <span class="text-4xl font-black text-[#FF6B35] tracking-tight">
                                {{ number_format($product->giaban ?? 0, 0, ',', '.') }} ₫
                            </span>
                        @endif
                    </div>

                    <div class="mb-8">
                        <style>
                            #desc-container::-webkit-scrollbar { width: 4px; }
                            #desc-container::-webkit-scrollbar-track { background: transparent; }
                            #desc-container::-webkit-scrollbar-thumb { background: #fed7aa; border-radius: 10px; }
                        </style>

                        <div id="desc-wrapper" class="relative bg-white rounded-xl">
                            <div id="desc-container" class="text-gray-600 leading-relaxed max-h-24 overflow-hidden transition-all duration-300 pr-2">
                                @php
                                    $description = $product->mota ?? "Một bó hoa tươi thắm thay cho vạn lời muốn nói.\nSản phẩm bao gồm:\n- Hoa hồng đỏ\n- Giấy gói cao cấp";
                                    $lines = preg_split('/\\r\\n|\\r|\\n/', $description);
                                    $showIcons = false;
                                @endphp

                                @foreach($lines as $line)
                                    @php $cleanLine = trim($line); @endphp
                                    @if($cleanLine === '') @continue @endif

                                    @if(stripos($cleanLine, 'sản phẩm bao gồm') !== false || stripos($cleanLine, 'chi tiết sản phẩm') !== false)
                                        @php $showIcons = true; @endphp
                                        <p class="font-bold text-gray-900 mt-4 mb-2">{{ $cleanLine }}</p>
                                        
                                    @elseif($showIcons || str_starts_with($cleanLine, '-') || str_starts_with($cleanLine, '*'))
                                        <div class="flex items-start gap-3 mb-2">
                                            <svg class="w-4 h-4 text-[#FF6B35] mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span class="text-gray-600">{{ ltrim($cleanLine, '-* ') }}</span>
                                        </div>
                                    @else
                                        <p class="mb-2 text-gray-600">{{ $cleanLine }}</p>
                                    @endif
                                @endforeach
                            </div>
                            
                            <div id="desc-fade" class="absolute bottom-0 left-0 w-full h-12 bg-gradient-to-t from-white to-transparent pointer-events-none transition-opacity duration-300"></div>
                        </div>

                        <button type="button" id="btn-toggle-desc" class="mt-2 text-[#FF6B35] font-bold text-sm hover:text-orange-600 transition flex items-center gap-1 bg-orange-50 px-3 py-1.5 rounded-lg w-fit">
                            <span>Xem toàn bộ mô tả</span>
                            <svg id="icon-toggle-desc" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    
                    <div class="flex flex-col xl:flex-row gap-4 mb-8">
                        <div class="flex items-center justify-between border-2 border-gray-100 rounded-2xl w-full xl:w-36 h-14 bg-white shadow-sm overflow-hidden focus-within:border-orange-200 transition-colors">
                            <button type="button" class="w-12 h-full flex items-center justify-center hover:bg-gray-50 btn-minus-qty text-gray-500 font-bold text-xl transition" {{ $tonKho <= 0 ? 'disabled' : '' }}>-</button>
                            <input type="text" id="product-quantity" class="w-10 text-center border-none focus:ring-0 font-bold text-gray-800 text-lg p-0 bg-transparent" value="{{ $tonKho > 0 ? 1 : 0 }}" readonly>
                            <button type="button" class="w-12 h-full flex items-center justify-center hover:bg-gray-50 btn-plus-qty text-gray-500 font-bold text-xl transition" {{ $tonKho <= 0 ? 'disabled' : '' }}>+</button>
                        </div>

                        <div class="flex flex-1 gap-4">
                            @if($tonKho > 0)
                                <form action="{{ route('cart.add', $product->masp) }}" method="POST" id="form-add-cart" class="w-1/3">
                                    @csrf
                                    <input type="hidden" name="quantity" id="hidden-quantity" value="1">
                                    <button type="submit" id="btn-add-cart" class="w-full h-14 bg-orange-50 border-2 border-orange-200 text-[#FF6B35] flex items-center justify-center rounded-2xl font-bold text-base hover:bg-orange-100 transition shadow-sm group">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </button>
                                </form>

                                <a href="{{ route('cart.buyNow', $product->masp) }}" id="btn-buy-now"
                                   class="flex-1 h-14 bg-gradient-to-r from-[#FF6B35] to-[#ff8559] text-white flex items-center justify-center rounded-2xl font-bold text-lg hover:shadow-lg hover:shadow-orange-200 transition duration-300 active:scale-95">
                                    Đặt Mua Ngay
                                </a>
                            @else
                                <button type="button" class="w-full h-14 bg-gray-200 text-gray-500 flex items-center justify-center rounded-2xl font-bold text-lg cursor-not-allowed" disabled>
                                    Tạm Hết Hàng
                                </button>
                            @endif
                        </div>
                    </div>              
                    
                    <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 mt-auto">
                        <h4 class="font-bold text-gray-800 text-sm mb-4 flex items-center gap-2 uppercase tracking-wide">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Cam kết từ SunFlower
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-4 text-sm text-gray-600">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-[#FF6B35] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>Giao hoa hỏa tốc trong <strong class="text-gray-800">2 giờ</strong></span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-[#FF6B35] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                                <span>Tặng kèm thiệp & Banner</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-[#FF6B35] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Chụp ảnh trước khi giao</span>
                            </div>
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-[#FF6B35] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.514"></path></svg>
                                <span>Cam kết hoa tươi từ 3 ngày</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KHỐI MÔ TẢ CHI TIẾT SẢN PHẨM --}}
        <div id="chi-tiet-mo-ta" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12 mb-12 scroll-mt-24">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-6 flex items-center gap-3">
                <span class="w-2 h-8 bg-[#FF6B35] rounded-full inline-block"></span>
                Mô tả
            </h2>
            
            <div class="relative">
                <div id="long-desc-container" class="text-gray-700 text-base leading-relaxed font-sans overflow-hidden product-content max-h-[400px] transition-all duration-700 relative">
                    @if(!empty($product->mota_chitiet))
                        @php
                            $imgUrl = str_starts_with($product->hinhanh, 'http') ? $product->hinhanh : asset('storage/' . ltrim($product->hinhanh, '/'));
                            $imgHtml = '<img src="' . $imgUrl . '" alt="' . $product->tensp . '" class="w-full max-w-md mx-auto rounded-2xl shadow-md my-8 block border border-gray-100">';
                            $finalContent = str_replace('[anh_hoa]', $imgHtml, $product->mota_chitiet);
                            $finalContent = strip_tags($finalContent, '<p><br><strong><em><ul><ol><li><h2><h3><img><a>');
                        @endphp
                        {!! $finalContent !!}
                    @else
                        <p class="text-gray-400 italic">Sản phẩm hiện chưa được cập nhật bài viết chi tiết.</p>
                    @endif
                </div>

                <div id="long-desc-fade" class="absolute bottom-0 left-0 w-full h-32 bg-gradient-to-t from-white to-transparent transition-opacity duration-300 pointer-events-none"></div>
            </div>

            <div class="mt-4 flex justify-center relative z-10">
                <button type="button" id="btn-toggle-long-desc" class="text-[#FF6B35] font-bold text-base hover:text-orange-600 transition flex items-center gap-2 bg-orange-50 px-6 py-2.5 rounded-xl w-fit hidden shadow-sm">
                    <span>Xem toàn bộ mô tả</span>
                    <svg id="icon-toggle-long-desc" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
            </div>

            <style>
                .product-content p { margin-bottom: 1rem; line-height: 1.8; }
                .product-content strong, .product-content b { color: #1f2937; }
                .product-content h3, .product-content h4 { color: #1f2937; font-weight: 800; margin-top: 2rem; margin-bottom: 1rem; font-size: 1.25rem; }
                .product-content ul { margin-left: 1.5rem; margin-bottom: 1.5rem; list-style-type: disc; }
                .product-content li { margin-bottom: 0.5rem; }
                .product-content .image-caption { text-align: center; font-style: italic; color: #6b7280; font-size: 0.875rem; margin-top: -1rem; margin-bottom: 2rem; }
            </style>
        </div>

        {{-- KHU VỰC HIỂN THỊ ĐÁNH GIÁ (CODE MỚI THÊM) --}}
        <div id="danh-gia-khach-hang" class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 md:p-12 mb-12 scroll-mt-24">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-[#FF6B35] rounded-full inline-block"></span>
                Đánh giá từ khách hàng
            </h2>

            @if($totalReviews > 0)
                {{-- Box thống kê chung --}}
                <div class="flex flex-col md:flex-row items-center gap-8 mb-10 bg-orange-50/40 p-8 rounded-[1rem] border border-orange-200 shadow-sm">
                    
                    {{-- Cột Trái: Điểm số --}}
                    <div class="text-center md:border-r md:border-orange-200 md:pr-10">
                        <div class="text-[#FF6B35] mb-2">
                            <span class="text-5xl font-black">{{ $avgRating }}</span>
                            <span class="text-xl font-bold text-orange-600"> trên 5</span>
                        </div>
                        <div class="flex items-center justify-center gap-1.5 mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                    <svg class="w-6 h-6 fill-current text-[#FF6B35]" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @else
                                    <svg class="w-6 h-6 fill-current text-orange-200" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endif
                            @endfor
                        </div>
                    </div>

                    {{-- Cột Phải: Các nút Lọc (Filter) --}}
                    <div class="flex-1 flex flex-wrap gap-3 justify-center md:justify-start">
                        @php $currentFilter = request('filter'); @endphp
                        
                        {{-- Nút Tất cả --}}
                        <a href="{{ request()->fullUrlWithQuery(['filter' => null]) }}#danh-gia-khach-hang" 
                           class="px-5 py-2 border rounded-sm text-sm font-medium transition-colors bg-white {{ !$currentFilter ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-gray-200 text-gray-800 hover:border-[#FF6B35]' }}">
                            Tất Cả
                        </a>
                        
                        {{-- Vòng lặp in nút 5 Sao -> 1 Sao --}}
                        @foreach([5, 4, 3, 2, 1] as $star)
                            <a href="{{ request()->fullUrlWithQuery(['filter' => $star]) }}#danh-gia-khach-hang"
                               class="px-5 py-2 border rounded-sm text-sm font-medium transition-colors bg-white {{ $currentFilter == $star ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-gray-200 text-gray-800 hover:border-[#FF6B35]' }}">
                                {{ $star }} Sao ({{ $countStars[$star] }})
                            </a>
                        @endforeach
                        
                        {{-- Nút Có Bình Luận --}}
                        <a href="{{ request()->fullUrlWithQuery(['filter' => 'comment']) }}#danh-gia-khach-hang"
                           class="px-5 py-2 border rounded-sm text-sm font-medium transition-colors bg-white {{ $currentFilter === 'comment' ? 'border-[#FF6B35] text-[#FF6B35]' : 'border-gray-200 text-gray-800 hover:border-[#FF6B35]' }}">
                            Có Bình Luận ({{ $countComments }})
                        </a>
                    </div>
                </div>

                {{-- Danh sách Comment --}}
                <div class="space-y-6">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-50 pb-8 last:border-0 last:pb-0">
                            <div class="flex items-start gap-4 mb-3">
                                {{-- Avatar tạo tự động từ chữ cái đầu của Tên --}}
                                <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#FF6B35] to-orange-300 flex-shrink-0 flex items-center justify-center text-white font-black text-xl shadow-md border-2 border-white">
                                    {{ substr($review->khachHang->hoten ?? 'K', 0, 1) }}
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-gray-900 text-lg">{{ $review->khachHang->hoten ?? 'Khách hàng ẩn danh' }}</h4>
                                        <span class="text-xs font-medium text-gray-400 bg-gray-50 px-3 py-1 rounded-full border border-gray-100">
                                            {{ date('d/m/Y', strtotime($review->created_at)) }}
                                        </span>
                                    </div>
                                    <div class="flex text-yellow-400 mt-1 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->so_sao)
                                                <svg class="w-4 h-4 fill-current text-yellow-400" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @else
                                                <svg class="w-4 h-4 fill-current text-gray-200" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            @endif
                                        @endfor
                                    </div>
                                    @if(!empty($review->binh_luan))
                                        <div class="bg-gray-50/80 p-5 rounded-2xl rounded-tl-none border border-gray-100 text-gray-700 leading-relaxed">
                                            {{ $review->binh_luan }}
                                        </div>
                                    @endif
                                    @if(!empty($review->phan_hoi))
                                        <div class="mt-4 bg-orange-50/60 p-4 rounded-2xl rounded-tl-none border border-orange-100 ml-4 relative">
                                            {{-- Dấu mũi tên chỉ lên (Tạo hiệu ứng box chat) --}}
                                            <div class="absolute -top-3 left-4 text-orange-50/60">
                                                <svg width="24" height="12" viewBox="0 0 24 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 0L24 12H0L12 0Z" />
                                                </svg>
                                            </div>
                                            
                                            <div class="flex items-center gap-2 mb-2">
                                                <div class="w-6 h-6 rounded-full bg-[#FF6B35] flex items-center justify-center text-white">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <span class="font-bold text-gray-900 text-sm">Phản hồi từ SunFlower</span>
                                            </div>
                                            <p class="text-gray-700 text-sm leading-relaxed pl-8">
                                                {{ $review->phan_hoi }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($reviews->hasPages())
                    <div class="mt-10 pt-6 border-t border-gray-100 flex justify-center">
                        {{-- Vì tôi thấy bạn có file pagination riêng ở resources/views/vendor/pagination/sunflower.blade.php nên bạn có thể gọi tên nó, hoặc dùng mặc định --}}
                        {{ $reviews->fragment('danh-gia-khach-hang')->links('vendor.pagination.sunflower') }}
                    </div>
                @endif
            @else
                <div class="text-center py-16 bg-gray-50 rounded-[2rem] border border-dashed border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Chưa có đánh giá nào</h3>
                    <p class="text-gray-500 font-medium">Hãy là người đầu tiên trải nghiệm và chia sẻ cảm nhận về sản phẩm này nhé!</p>
                </div>
            @endif
        </div>

        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-8 border-t border-gray-100 pt-10">
            <h2 class="text-2xl font-extrabold text-gray-900 mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-[#FF6B35] rounded-full inline-block"></span>
                Sản phẩm tương tự
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $related)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 group flex flex-col h-full hover:-translate-y-1">
                        <a href="{{ route('product.show', $related->masp) }}" class="relative w-full aspect-square overflow-hidden bg-gray-50 block">
                            <img src="{{ str_starts_with($related->hinhanh, 'http') ? $related->hinhanh : asset('storage/' . ltrim($related->hinhanh, '/')) }}" 
                                alt="{{ $related->tensp }}" 
                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="bg-white text-gray-900 text-sm font-bold py-2 px-4 rounded-full shadow-lg transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">Xem hoa</span>
                            </div>
                        </a>
                        
                        <div class="p-4 flex flex-col flex-grow relative bg-white">
                            <h3 class="text-gray-800 font-bold text-sm mb-3 line-clamp-2 group-hover:text-[#FF6B35] transition-colors leading-relaxed">
                                <a href="{{ route('product.show', $related->masp) }}">{{ $related->tensp }}</a>
                            </h3>
                            <div class="mt-auto">
                                @if(!empty($related->giakm) && $related->giakm < $related->giaban)
                                    <div class="flex flex-col">
                                        <span class="text-[#FF6B35] font-extrabold text-lg">{{ number_format($related->giakm, 0, ',', '.') }} ₫</span>
                                        <span class="text-gray-400 line-through text-xs font-medium">{{ number_format($related->giaban, 0, ',', '.') }} ₫</span>
                                    </div>
                                @else
                                    <span class="text-[#FF6B35] font-extrabold text-lg">{{ number_format($related->giaban ?? 0, 0, ',', '.') }} ₫</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const inputQty = document.getElementById('product-quantity');
                const btnMinus = document.querySelector('.btn-minus-qty');
                const btnPlus = document.querySelector('.btn-plus-qty');
                const btnBuyNow = document.getElementById('btn-buy-now');

                if (!inputQty || !btnMinus || !btnPlus) return;

                const baseBuyNowUrl = "{{ route('cart.buyNow', $product->masp) }}";
                const hiddenQty = document.getElementById('hidden-quantity');
                const maxQty = {{ $tonKho }}; // Giới hạn tồn kho

                function updateLinks() {
                    const qty = inputQty.value;
                    if(hiddenQty) hiddenQty.value = qty;
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
                    if (qty < maxQty) {
                        inputQty.value = qty + 1;
                        updateLinks();
                    } else {
                        alert('Xin lỗi, số lượng sản phẩm trong kho chỉ còn ' + maxQty + ' đóa hoa!');
                    }
                });

                updateLinks();

                // Xử lý mô tả rút gọn
               const longDescContainer = document.getElementById('long-desc-container');
                const longDescFade = document.getElementById('long-desc-fade');
                const btnToggleLongDesc = document.getElementById('btn-toggle-long-desc');
                const iconToggleLongDesc = document.getElementById('icon-toggle-long-desc');

                if (longDescContainer && btnToggleLongDesc) {
                    if (longDescContainer.scrollHeight > 400) {
                        btnToggleLongDesc.classList.remove('hidden');
                    } else {
                        if(longDescFade) longDescFade.style.display = 'none'; 
                    }

                    btnToggleLongDesc.addEventListener('click', () => {
                        const isExpanded = longDescContainer.classList.contains('max-h-[5000px]');
                        
                        if (!isExpanded) {
                            longDescContainer.classList.remove('max-h-[400px]');
                            longDescContainer.classList.add('max-h-[5000px]');
                            if(longDescFade) longDescFade.style.opacity = '0'; 
                            
                            btnToggleLongDesc.querySelector('span').innerText = 'Thu gọn mô tả';
                            iconToggleLongDesc.classList.add('rotate-180');
                        } else {
                            longDescContainer.classList.remove('max-h-[5000px]');
                            longDescContainer.classList.add('max-h-[400px]');
                            if(longDescFade) longDescFade.style.opacity = '1'; 
                            
                            btnToggleLongDesc.querySelector('span').innerText = 'Xem toàn bộ mô tả';
                            iconToggleLongDesc.classList.remove('rotate-180');
                            
                            document.getElementById('chi-tiet-mo-ta').scrollIntoView({ behavior: 'smooth' });
                        }
                    });
                }
                const descContainer = document.getElementById('desc-container');
                const descFade = document.getElementById('desc-fade');
                const btnToggleDesc = document.getElementById('btn-toggle-desc');
                const iconToggleDesc = document.getElementById('icon-toggle-desc');

                if (descContainer && btnToggleDesc) {
                    if (descContainer.scrollHeight <= 96) {
                        btnToggleDesc.style.display = 'none';
                        if(descFade) descFade.style.display = 'none';
                    }

                    btnToggleDesc.addEventListener('click', () => {
                        const isExpanded = descContainer.classList.contains('max-h-[1000px]');
                        
                        if (!isExpanded) {
                            descContainer.classList.remove('max-h-24');
                            descContainer.classList.add('max-h-[1000px]');
                            if(descFade) descFade.style.opacity = '0';
                            
                            btnToggleDesc.querySelector('span').innerText = 'Thu gọn';
                            iconToggleDesc.classList.add('rotate-180');
                        } else {
                            descContainer.classList.remove('max-h-[1000px]');
                            descContainer.classList.add('max-h-24');
                            if(descFade) descFade.style.opacity = '1';
                            
                            btnToggleDesc.querySelector('span').innerText = 'Xem toàn bộ mô tả';
                            iconToggleDesc.classList.remove('rotate-180');
                        }
                    });
                }
            });
        </script>
    @else
        <div class="text-center py-32 bg-gray-50 rounded-3xl border border-dashed border-gray-200">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Sản phẩm không tồn tại!</h2>
            <a href="{{ route('home') }}" class="inline-block bg-[#FF6B35] text-white font-bold px-8 py-3.5 rounded-xl">Về Trang Chủ</a>
        </div>
    @endif
</div>
@endsection