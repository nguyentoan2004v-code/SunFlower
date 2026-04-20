@extends('layouts.app')

@section('title', 'Đăng Nhập - SunFlower')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center py-12 px-4" 
     style="background-image: url('{{ asset('storage/image/login/nenlogin.png') }}'); background-color: #fffaf0;">
    
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 border border-orange-50">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-[#E67E22] mb-2">Đăng Nhập</h2>
            <p class="text-gray-500 text-sm">Vui lòng đăng nhập để tiếp tục mua sắm.</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            
            <input type="text" name="prevent_autofill" id="prevent_autofill" value="" style="display:none;" />
            <input type="password" name="password_fake" id="password_fake" value="" style="display:none;" />

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Email của bạn"
                       autocomplete="off"> </div>

            <div class="relative">
                <label class="block text-sm font-bold text-gray-700 mb-1">Mật khẩu</label>
                <input type="password" id="login-password" name="password" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Mật khẩu"
                       autocomplete="new-password"> <button type="button" onclick="togglePassword('login-password', 'eye-icon-login')" class="absolute right-4 top-9 text-gray-400 hover:text-[#FF6B35] transition">
                    <svg id="eye-icon-login" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <button type="submit" class="w-full bg-[#E67E22] hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-200 transition active:scale-95">
                Đăng Nhập
            </button>

            <p class="text-center text-sm text-gray-600">
                Chưa có tài khoản? <a href="{{ route('register') }}" class="text-[#E67E22] font-bold hover:underline">Đăng ký ngay</a>
            </p>
        </form>
    </div>
</div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.add('text-orange-500');
    } else {
        input.type = "password";
        icon.classList.remove('text-orange-500');
    }
}
</script>
@endsection