@extends('layouts.app')

@section('title', 'Đăng Ký Tài Khoản - SunFlower')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-cover bg-center py-12 px-4" 
     style="background-image: url('{{ asset('images/bg-sunflower.jpg') }}'); background-color: #fffaf0;">
    
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-10 border border-orange-50">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-extrabold text-[#E67E22] mb-2">Đăng Ký Tài Khoản</h2>
            <p class="text-gray-500 text-sm">Tạo tài khoản để nhận nhiều ưu đãi và quản lý đơn hàng tốt hơn.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-6" autocomplete="off">
            @csrf
            
            <input type="text" style="display:none">
            <input type="password" style="display:none">

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Họ tên *</label>
                <input type="text" name="hoten" value="{{ old('hoten') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Họ tên *"
                       autocomplete="off">
                @error('hoten') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Email *"
                       autocomplete="off">
                @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Số điện thoại *</label>
                <input type="text" name="sdt" value="{{ old('sdt') }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Số điện thoại *"
                       autocomplete="off">
                @error('sdt') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="relative">
                <label class="block text-sm font-bold text-gray-700 mb-1">Mật khẩu *</label>
                <input type="password" id="password" name="password" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Mật khẩu"
                       autocomplete="new-password"> <button type="button" onclick="togglePassword('password', 'eye-icon-1')" class="absolute right-4 top-9 text-gray-400 hover:text-[#FF6B35] transition">
                    <svg id="eye-icon-1" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="relative">
                <label class="block text-sm font-bold text-gray-700 mb-1">Xác nhận mật khẩu *</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-orange-200 focus:border-[#FF6B35] outline-none transition" 
                       placeholder="Xác nhận mật khẩu"
                       autocomplete="new-password">
                
                <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="absolute right-4 top-9 text-gray-400 hover:text-[#FF6B35] transition">
                    <svg id="eye-icon-2" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>

            <button type="submit" class="w-full bg-[#E67E22] hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-orange-200 transition active:scale-95">
                Đăng Ký Tài Khoản
            </button>

            <p class="text-center text-sm text-gray-600">
                Đã có tài khoản? <a href="{{ route('login') }}" class="text-[#E67E22] font-bold hover:underline">Đăng nhập ngay</a>
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