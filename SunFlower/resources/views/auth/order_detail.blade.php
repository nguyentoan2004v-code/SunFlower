@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng ' . $donHang->madon)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('orders.history') }}" class="text-sm text-gray-500 hover:text-[#FF6B35] flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Quay lại lịch sử
            </a>
            <h1 class="text-3xl font-extrabold text-gray-900">Chi tiết đơn hàng #{{ $donHang->madon }}</h1>
        </div>
        
        @if($donHang->trangthai == 'Chờ xác nhận')
            <form action="{{ route('orders.cancel', $donHang->madon) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                @csrf
                <button type="submit" class="bg-white border-2 border-red-500 text-red-500 hover:bg-red-50 font-bold py-2 px-6 rounded-xl transition">
                    Hủy đơn hàng
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm mb-8">
        @php
            // Mảng các bước hiển thị trên giao diện khách hàng
            $statuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao'];
            
            // Logic khớp trạng thái DB của Admin với giao diện 4 bước
            $dbStatus = $donHang->trangthai;
            $currentIndex = 0; // Mặc định Bước 1
            
            if ($dbStatus == 'Đang giao') {
                $currentIndex = 2; // Sáng tới Bước 3
            } elseif ($dbStatus == 'Đã hoàn thành') {
                $currentIndex = 3; // Sáng hết Bước 4
            }
            
            $isCancelled = ($dbStatus == 'Đã hủy');
        @endphp

        @if($isCancelled)
            <div class="flex items-center justify-center gap-2 text-red-600 font-bold py-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                ĐƠN HÀNG ĐÃ BỊ HỦY
            </div>
        @else
            <div class="relative flex justify-between items-center w-full">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-gray-100 w-full rounded-full"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-[#FF6B35] transition-all duration-500 rounded-full" 
                     style="width: {{ $currentIndex * 33.33 }}%"></div>

                @foreach($statuses as $index => $label)
                    @php
                        // Cấu hình màu sắc các bước
                        $colorClass = 'bg-gray-300';
                        if ($index <= $currentIndex) {
                            $colorClass = match($label) {
                                'Chờ xác nhận' => 'bg-yellow-400',
                                'Đã xác nhận'  => 'bg-blue-500',
                                'Đang giao'    => 'bg-purple-500',
                                'Đã giao'      => 'bg-green-500',
                                default        => 'bg-gray-300'
                            };
                        }
                    @endphp
                    <div class="relative z-10 flex flex-col items-center">
                       <div class="w-10 h-10 {{ $colorClass }} rounded-full flex items-center justify-center text-white shadow-lg transition-colors duration-500">
                            {{-- Hiện dấu tick nếu là các bước đã qua, HOẶC nếu đơn hàng đã hoàn thành thì tick luôn bước cuối --}}
                            @if($index <= $currentIndex)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span>{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="absolute top-12 text-xs font-bold whitespace-nowrap {{ $index <= $currentIndex ? 'text-gray-900' : 'text-gray-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
                <h3 class="font-bold text-lg mb-4">Thông tin nhận hàng</h3>
                <div class="space-y-3 text-gray-600">
                    <p><span class="font-medium text-gray-900">Số điện thoại:</span> {{ $donHang->sdt_nhan }}</p>
                    <p><span class="font-medium text-gray-900">Địa chỉ:</span> {{ $donHang->diachi_giao }}</p>
                    <p><span class="font-medium text-gray-900">Ghi chú:</span> {{ $donHang->ghichu ?? 'Không có' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm">
            <h3 class="font-bold text-lg mb-4">Sản phẩm đã đặt</h3>
            <div class="space-y-4">
                @foreach($donHang->sanphams as $sp)
                    <div class="flex items-center gap-4">
                        <img src="{{ route('product.image', $sp->masp) }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900">{{ $sp->tensp }}</p>
                            <p class="text-sm text-gray-500">x{{ $sp->pivot->soluong }}</p>
                        </div>
                        <p class="font-bold">{{ number_format($sp->pivot->giaban, 0, ',', '.') }} ₫</p>
                    </div>
                @endforeach
                <div class="border-t pt-4 flex justify-between items-center">
                    <span class="font-bold text-gray-900">Tổng thanh toán</span>
                    <span class="text-2xl font-extrabold text-[#FF6B35]">{{ number_format($donHang->tongtien, 0, ',', '.') }} ₫</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection