@extends('layouts.app')

@section('title', 'Đăng nhập - SunFlower')

@section('content')
<div class="max-w-md mx-auto mt-16 bg-white p-8 border rounded-2xl shadow-sm">
    <h2 class="text-3xl font-bold text-center mb-8 text-gray-800">Đăng Nhập</h2>

    @if($errors->has('login_error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errors->first('login_error') }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Mật khẩu</label>
            <input type="password" name="password" required
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#FF6B35] focus:ring-1 focus:ring-[#FF6B35]">
        </div>

        <button type="submit" class="w-full bg-[#FF6B35] text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition">
            Đăng Nhập
        </button>
    </form>

    <p class="text-center mt-6 text-gray-600">
        Chưa có tài khoản? <a href="{{ route('register') }}" class="text-[#FF6B35] font-bold hover:underline">Đăng ký ngay</a>
    </p>
</div>
@endsection