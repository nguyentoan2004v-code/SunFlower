@extends('layouts.app')

@section('title', 'Tìm kiếm: ' . $keyword)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[60vh]">
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#FF6B35]">Trang chủ</a> / <span class="font-bold text-gray-800">Tìm kiếm</span>
    </nav>

    <div class="mb-10 border-b pb-4">
        <h2 class="text-2xl">Kết quả cho: <span class="font-bold text-[#FF6B35]">"{{ $keyword }}"</span></h2>
        <p class="text-gray-500 mt-1">Tìm thấy {{ count($products) }} đóa hoa phù hợp.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
        @if(!empty($products) && count($products) > 0)
            @foreach($products as $item)
                @php $product = (array) $item; @endphp
                @if(isset($product['masp']))
                    <div class="bg-white border border-gray-100 rounded-2xl p-4 flex flex-col group shadow-sm hover:shadow-lg transition duration-300">
                        <a href="{{ route('product.show', $product['masp']) }}" class="aspect-square overflow-hidden rounded-xl mb-4 relative">
                            {{-- DB bro thiếu cột hinhanh nên tui dùng ảnh mẫu từ Unsplash nhé --}}
                            <img src="{{ !empty($product['hinhanh']) ? asset('storage/' . $product['hinhanh']) : 'https://images.unsplash.com/photo-1526047932273-341f2a7631f9?q=80&w=500' }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                        <h3 class="font-bold text-gray-800 text-center mb-2 line-clamp-1">
                            <a href="{{ route('product.show', $product['masp']) }}">{{ $product['tensp'] }}</a>
                        </h3>
                        <p class="text-[#FF6B35] font-black text-center text-xl mb-4">
                            {{ number_format($product['giaban'], 0, ',', '.') }} đ
                        </p>
                        <a href="{{ route('cart.add', $product['masp']) }}" 
                           class="mt-auto w-full bg-gray-50 text-[#FF6B35] border border-[#FF6B35] hover:bg-[#FF6B35] hover:text-white text-center py-2.5 rounded-xl font-bold transition">
                            Thêm vào giỏ
                        </a>
                    </div>
                @endif
            @endforeach
        @else
            <div class="col-span-full text-center py-20 bg-gray-50 rounded-3xl border border-dashed">
                <p class="text-gray-500 text-lg">Không tìm thấy bông hoa nào tên "{{ $keyword }}" cả bro ơi! 🥀</p>
            </div>
        @endif
    </div>
</div>
@endsection