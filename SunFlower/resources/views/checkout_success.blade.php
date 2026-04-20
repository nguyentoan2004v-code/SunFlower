@extends('layouts.app')

@section('title', 'Đặt hàng thành công - SunFlower')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl p-8 text-center border border-gray-100 transform transition-all hover:scale-[1.01]">
        
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
            <svg class="h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-3">Đặt hoa thành công!</h1>
        <p class="text-gray-500 mb-8 leading-relaxed">
            Cảm ơn bạn đã tin tưởng <strong>SunFlower</strong>. Chúng tôi đang xử lý đơn hàng của bạn và sẽ giao những đóa hoa tươi thắm nhất đến đúng hẹn.
        </p>

        <div class="bg-orange-50 rounded-2xl p-6 mb-8 border border-orange-100 shadow-inner">
            <p class="text-sm font-semibold text-gray-600 mb-2 uppercase tracking-widest">Mã đơn hàng của bạn</p>
            <p class="text-3xl font-extrabold text-[#FF6B35] tracking-wider">{{ $maDon }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('home') }}" class="inline-flex justify-center items-center px-6 py-3.5 border border-transparent text-base font-bold rounded-xl text-white bg-[#FF6B35] hover:bg-orange-600 transition shadow-lg shadow-orange-200 active:scale-95">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Về trang chủ
            </a>
            <a href="{{ route('orders.show', $maDon) }}" class="inline-flex justify-center items-center px-6 py-3.5 border-2 border-gray-200 text-base font-bold rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition active:scale-95">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Xem đơn hàng
            </a>
        </div>
    </div>
</div>
@endsection