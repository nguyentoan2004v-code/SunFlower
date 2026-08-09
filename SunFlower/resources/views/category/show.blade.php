@extends('layouts.app')

@section('title', ($category->tendm ?? 'Danh mục sản phẩm') . ' - SunFlower')

@section('content')
<div class="bg-gray-50 min-h-screen pb-20">
    <section class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-12">
            <nav class="flex mb-4 text-sm text-gray-400 font-medium">
                <a href="{{ route('home') }}" class="hover:text-[#FF6B35] transition">Trang chủ</a>
                <span class="mx-2">/</span>
                <a href="{{ route('categories.index') }}" class="hover:text-[#FF6B35] transition">Danh mục</a>
                <span class="mx-2">/</span>
                <span class="text-gray-600">{{ $category->tendm ?? '' }}</span>
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
                        {{ $category->tendm ?? 'Tất cả sản phẩm' }} 
                        <span class="text-sm font-normal text-gray-400 ml-2">({{ $products->total() }} sản phẩm)</span>
                    </h2>
                </div>

                @if($products->count() > 0)
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-10 md:gap-x-6">
                        @foreach($products as $product)
                            @include('partials.product-card', ['product' => $product])
                        @endforeach
                    </div>
                    <div class="mt-8 flex justify-center">
                        {{ $products->withQueryString()->links() }}
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