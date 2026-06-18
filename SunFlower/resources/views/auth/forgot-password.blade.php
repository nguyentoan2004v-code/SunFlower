@extends('layouts.app')

@section('title', 'Quên Mật Khẩu - SunFlower')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center py-12 px-4"
     style="background-image: url('{{ asset('https://res.cloudinary.com/drgrh0yeo/image/upload/v1780328818/sunflower_login/wztpg0fg2zyvp408u6zv.png') }}'); background-color: #fffaf0;">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 border border-orange-50">

        {{-- Icon + Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-orange-100">
                <svg class="w-8 h-8 text-[#E67E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-[#E67E22] mb-2">Quên Mật Khẩu</h2>
            <p class="text-gray-500 text-sm leading-relaxed">
                Nhập email đã đăng ký. Chúng tôi sẽ gửi link đặt lại mật khẩu về hộp thư của bạn.
            </p>
        </div>

        {{-- Thông báo thành công --}}
        @if (session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl flex items-start gap-3">
                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-700 text-sm font-medium">{{ session('status') }}</p>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5" id="forgot-form" data-loading>
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1" for="forgot-email">
                    Địa chỉ Email
                </label>
                <input type="email"
                       id="forgot-email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition @error('email') border-red-500 focus:ring-red-200 focus:border-red-500 @enderror"
                       placeholder="email@example.com">

                @error('email')
                    <span class="text-red-500 text-sm mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit"
                    id="btn-send-reset"
                    class="w-full bg-[#E67E22] hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-200 transition active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                Gửi Link Đặt Lại Mật Khẩu
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}"
               class="text-sm text-gray-500 hover:text-[#E67E22] transition font-medium flex items-center justify-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại Đăng Nhập
            </a>
        </div>
    </div>
</div>
@endsection
