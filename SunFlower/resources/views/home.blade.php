@extends('layouts.app')

@section('title', 'Trang chủ - SunFlower')

@section('content')
<div class="space-y-20 pb-16">
    
    <section class="relative w-full min-h-[500px] flex items-center bg-cover bg-center"
             style="background-image: url('{{ $heroImage ?? asset('images/default-banner.jpg') }}');">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 text-white">
            <h1 class="text-6xl font-extrabold mb-6">SunFlower<br>Hoa Tươi Mỗi Ngày</h1>
            <a href="#products" class="bg-[#FF6B35] px-8 py-3 rounded-md font-bold">Mua Ngay</a>
        </div>
    </section>

    @if(isset($apiError) && $apiError)
    <section class="max-w-7xl mx-auto px-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Lỗi kết nối!</strong>
            <span class="block sm:inline">Không thể tải dữ liệu từ máy chủ. Vui lòng kiểm tra lại kết nối hoặc thử lại sau.</span>
            <p class="text-sm mt-2">Đảm bảo rằng Backend API đang chạy tại <code class="font-mono">http://127.0.0.1:8000</code>.</p>
        </div>
    </section>
    @endif 
    <section class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-10 text-center">Tất Cả Danh Mục</h2>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @if(is_array($categories))
                @foreach($categories as $category)
                    @if(is_array($category) && isset($category['madm']))
                        <a href="{{ route('category.show', $category['madm']) }}" class="relative h-48 rounded-xl overflow-hidden group shadow-md block">
                            <img src="{{ asset('storage/' . ($category['hinhanh'] ?? '')) }}" class="w-full h-full object-cover group-hover:scale-110 transition">
                            <div class="absolute inset-0 bg-black/40"></div>
                            <h3 class="absolute bottom-4 left-4 text-white font-bold">{{ $category['tendm'] ?? 'Danh mục' }}</h3>
                        </a>
                    @endif
                @endforeach
            @endif
        </div>
    </section>

    <section id="products" class="max-w-7xl mx-auto px-4">
        <h2 class="text-3xl font-bold mb-10 text-center">Sản Phẩm Mới</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @if(is_array($products))
                @foreach($products as $product)
                    @if(is_array($product) && isset($product['masp']))
                        <div class="border rounded-2xl p-4 flex flex-col group shadow-sm">
                            <a href="{{ route('product.show', $product['masp']) }}" class="aspect-square overflow-hidden rounded-xl mb-4">
                                <img src="{{ asset('storage/' . ($product['hinhanh'] ?? '')) }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                            </a>
                            <h3 class="font-bold text-center mb-2">{{ $product['tensp'] ?? 'Tên hoa' }}</h3>
                            <p class="text-[#FF6B35] font-black text-center text-xl mb-4">
                                {{ number_format($product['giaban'] ?? 0, 0, ',', '.') }} đ
                            </p>
                            <a href="{{ route('cart.add', $product['masp']) }}" class="bg-[#FF6B35] text-white text-center py-3 rounded-xl font-bold">Thêm vào giỏ</a>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </section>
</div>
@endsection