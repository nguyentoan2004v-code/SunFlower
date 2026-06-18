@extends('layouts.app')

@section('title', 'Đặt Lại Mật Khẩu - SunFlower')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center py-12 px-4"
     style="background-image: url('{{ asset('https://res.cloudinary.com/drgrh0yeo/image/upload/v1780328818/sunflower_login/wztpg0fg2zyvp408u6zv.png') }}'); background-color: #fffaf0;">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 border border-orange-50">

        {{-- Icon + Header --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-orange-100">
                <svg class="w-8 h-8 text-[#E67E22]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-[#E67E22] mb-2">Đặt Lại Mật Khẩu</h2>
            <p class="text-gray-500 text-sm">Nhập mật khẩu mới cho tài khoản của bạn.</p>
        </div>

        <form action="{{ route('password.update') }}" method="POST" class="space-y-5" id="reset-form" data-loading>
            @csrf

            {{-- Token ẩn — bắt buộc để Laravel verify link hợp lệ --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email ẩn — bắt buộc để broker tìm đúng user --}}
            <input type="hidden" name="email" value="{{ $email }}">

            {{-- Thông báo lỗi chung --}}
            @error('email')
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-red-600 text-sm font-medium">{{ $message }}</p>
                </div>
            @enderror

            {{-- Mật khẩu mới --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1" for="new-password">
                    Mật khẩu mới
                </label>
                <div class="relative">
                    <input type="password"
                           id="new-password"
                           name="password"
                           required
                           minlength="6"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition @error('password') border-red-500 @enderror"
                           placeholder="Tối thiểu 6 ký tự">
                    <button type="button"
                            onclick="togglePassword('new-password', 'eye-new')"
                            class="absolute right-4 top-3 text-gray-400 hover:text-[#FF6B35] transition">
                        <svg id="eye-new" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="text-red-500 text-sm mt-1 block font-medium">{{ $message }}</span>
                @enderror
            </div>

            {{-- Xác nhận mật khẩu — name="password_confirmation" bắt buộc cho rule 'confirmed' --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1" for="confirm-password">
                    Xác nhận mật khẩu mới
                </label>
                <div class="relative">
                    <input type="password"
                           id="confirm-password"
                           name="password_confirmation"
                           required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition"
                           placeholder="Nhập lại mật khẩu mới">
                    <button type="button"
                            onclick="togglePassword('confirm-password', 'eye-confirm')"
                            class="absolute right-4 top-3 text-gray-400 hover:text-[#FF6B35] transition">
                        <svg id="eye-confirm" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                    id="btn-reset-password"
                    class="w-full bg-[#E67E22] hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-200 transition active:scale-95 flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Xác Nhận Đặt Lại Mật Khẩu
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.add('text-orange-500');
    } else {
        input.type = 'password';
        icon.classList.remove('text-orange-500');
    }
}
</script>
@endsection
