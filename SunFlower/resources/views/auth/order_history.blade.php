@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng của bạn')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
            <svg class="w-8 h-8 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            Lịch sử đơn hàng
        </h1>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
        @if($donHangs->count() > 0)
            <div class="space-y-8">
                @foreach($donHangs as $don)
    <a href="{{ route('orders.show', $don->madon) }}" class="block border border-gray-100 rounded-3xl overflow-hidden hover:border-[#FF6B35] hover:shadow-lg transition-all duration-300 mb-8 group bg-white">
        
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center border-b border-gray-100 group-hover:bg-orange-50/50 transition-colors">
            <div>
                <p class="font-bold text-gray-900 text-lg">
                    Mã đơn: <span class="text-[#FF6B35] group-hover:underline">{{ $don->madon }}</span>
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Ngày đặt: {{ \Carbon\Carbon::parse($don->ngaydat)->format('d/m/Y H:i') }}
                </p>
            </div>
            
            <span class="px-5 py-2 rounded-full text-sm font-bold border
                {{ $don->trangthai == 'Chờ xác nhận' ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : '' }}
                {{ $don->trangthai == 'Đã xác nhận' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                {{ $don->trangthai == 'Đang giao' ? 'bg-purple-50 text-purple-700 border-purple-200' : '' }}
                {{ $don->trangthai == 'Đã giao' ? 'bg-green-50 text-green-700 border-green-200' : '' }}
                {{ $don->trangthai == 'Đã hủy' ? 'bg-red-50 text-red-700 border-red-200' : '' }}">
                {{ $don->trangthai }}
            </span>
        </div>

        <div class="px-6 py-2 divide-y divide-gray-50">
            @foreach($don->sanphams as $sp)
                <div class="flex items-center gap-6 py-4">
                    <img src="{{ route('product.image', $sp->masp) }}" class="w-16 h-16 object-cover rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900">{{ $sp->tensp }}</h4>
                        <p class="text-sm text-gray-500 mt-1 font-medium">Số lượng: x{{ $sp->pivot->soluong }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-gray-900">{{ number_format($sp->pivot->giaban, 0, ',', '.') }} ₫</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="bg-white px-6 py-5 flex justify-between items-center border-t border-gray-100">
            <span class="text-gray-500 font-medium">Click để xem chi tiết đơn hàng</span>
            <div class="flex items-center gap-3">
                <span class="text-gray-600 font-medium">Thành tiền:</span>
                <span class="text-2xl font-extrabold text-[#FF6B35]">{{ number_format($don->tongtien, 0, ',', '.') }} ₫</span>
            </div>
        </div>
    </a>
@endforeach
            </div>
        @else
            <div class="text-center py-20">
                <p>Bạn chưa có đơn hàng nào.</p>
            </div>
        @endif
    </div>
</div>
@endsection