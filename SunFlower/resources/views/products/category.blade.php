@extends('layouts.app')

@section('title', $category->tendanhmuc)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <div class="text-center mb-16">
        <h1 class="text-4xl font-bold font-serif text-gray-900 uppercase tracking-widest">
            {{ $category->tendanhmuc }}
        </h1>
        <div class="h-1 w-20 bg-[#FF6B35] mx-auto mt-4"></div>
    </div>

    @if($products->isEmpty())
        <p class="text-center text-gray-500">Hiện chưa có sản phẩm nào trong danh mục này.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif
</div>
@endsection