@if ($paginator->hasPages())
    <div class="flex flex-col md:flex-row items-center justify-between w-full gap-4 mt-8 border-t border-gray-100 pt-6">
        <div class="text-sm text-gray-500 italic">
            Hiển thị từ <span class="font-bold text-gray-800">{{ $paginator->firstItem() }}</span> đến <span class="font-bold text-gray-800">{{ $paginator->lastItem() }}</span> trong <span class="font-bold text-[#FF6B35]">{{ $paginator->total() }}</span> sản phẩm
        </div>

        <div class="flex items-center gap-2">
            {{-- Nút Trước --}}
            @if ($paginator->onFirstPage())
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed border border-gray-100">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-[#FF6B35] border border-orange-100 hover:bg-[#FF6B35] hover:text-white transition shadow-sm">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
            @endif

            {{-- Các số trang --}}
            @foreach ($elements as $element)
                {{-- Dấu 3 chấm --}}
                @if (is_string($element))
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-400 border border-gray-100">
                        {{ $element }}
                    </span>
                @endif

                {{-- Link các trang --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-[#FF6B35] text-white font-bold shadow-lg shadow-orange-200 border border-[#FF6B35]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-gray-600 font-bold border border-gray-100 hover:border-orange-200 hover:text-[#FF6B35] hover:bg-orange-50 transition shadow-sm">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Nút Tiếp --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white text-[#FF6B35] border border-orange-100 hover:bg-[#FF6B35] hover:text-white transition shadow-sm">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 cursor-not-allowed border border-gray-100">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
    </div>
@endif