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
        </div>
        <p class="text-gray-500 mt-1">Tìm thấy {{ $products->total() }} đóa hoa phù hợp.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-x-4 gap-y-10 md:gap-x-6">
        @if(!empty($products) && count($products) > 0)
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
            <div class="col-span-full mt-8 flex justify-center">
                {{ $products->withQueryString()->links() }}
            </div>
        @else
            <div class="col-span-full text-center py-20 bg-gray-50 rounded-3xl border border-dashed">
                <div class="text-5xl mb-4">🥀</div>
                <p class="text-gray-700 text-lg font-bold mb-2">Không tìm thấy bông hoa nào!</p>
                <p class="text-gray-400 text-sm mb-6">
                    Không có sản phẩm nào phù hợp với <strong>"{{ $keyword }}"</strong>
                </p>
                <a href="{{ route('home') }}" class="inline-block bg-[#FF6B35] text-white px-6 py-3 rounded-xl font-bold hover:bg-orange-600 transition">
                    Quay về trang chủ
                </a>
            </div>
        @endif
    </div>
</div>
@endsection