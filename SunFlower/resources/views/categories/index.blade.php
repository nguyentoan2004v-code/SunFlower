@extends('layouts.app')

@section('title', 'Danh mục - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10 min-h-[60vh]">
    <nav class="text-sm text-gray-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Trang chủ</a>
        <span class="mx-2">/</span>
        <span class="font-bold text-gray-800">Tất cả danh mục</span>
    </nav>

    <div class="text-center mb-12">
        <span class="text-xs font-semibold tracking-[0.2em] text-[#FF6B35] uppercase">Khám phá</span>
        <h2 class="font-['Fraunces'] text-3xl md:text-4xl font-medium text-gray-900 mt-2">Chọn Hoa Theo Chủ Đề</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
        @if(isset($categories) && $categories->count() > 0)
            @foreach($categories as $category)
                @php
                    $catImg = asset('images/default-flower.jpg');
                    if(!empty($category->hinhanh)){
                        $catImg = str_starts_with($category->hinhanh, 'http') ? $category->hinhanh : asset('storage/' . ltrim($category->hinhanh, '/'));
                    }
                @endphp
                <a href="{{ route('category.show', $category->madm) }}" 
                   class="group relative block h-64 overflow-hidden rounded-2xl bg-gray-100">
                    <img src="{{ $catImg }}" 
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" 
                         alt="{{ $category->tendm ?? 'Danh mục' }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/5 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 p-5">
                        <h3 class="font-['Fraunces'] text-white font-medium text-lg leading-tight">
                            {{ $category->tendm ?? 'Danh mục' }}
                        </h3>
                        <span class="inline-block mt-2 h-[2px] w-6 bg-[#FF6B35] transition-all duration-300 group-hover:w-10"></span>
                    </div>
                </a>
            @endforeach
        @else
            <p class="col-span-full text-center text-gray-500 py-10">Chưa có danh mục nào được cập nhật.</p>
        @endif
    </div>
</div>
@endsection