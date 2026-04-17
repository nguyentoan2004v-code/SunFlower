@extends('layouts.app')

@section('title', 'Đăng ký - SunFlower')

@section('content')
<div class="max-w-md mx-auto mt-16 bg-white p-8 border rounded-2xl shadow-sm mb-16">
    <h2 class="text-3xl font-bold text-center mb-8">Tạo Tài Khoản</h2>

    @if($errors->has('msg'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ $errors->first('msg') }}
        </div>
    @endif

    <form action="{{ url('/dang-ky') }}" method="POST" class="space-y-5">
        @csrf
        
        <div>
            <label class="block text-gray-700 font-bold mb-2">Họ và Tên</label>
            <input type="text" name="hoten" value="{{ old('hoten') }}" required
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#FF6B35]">
            @error('hoten') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Số điện thoại</label>
            <input type="text" name="sdt" value="{{ old('sdt') }}" required
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#FF6B35]">
            @error('sdt') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#FF6B35]">
            @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Mật khẩu</label>
            <input type="password" name="password" required minlength="6"
                   class="w-full px-4 py-3 rounded-xl border focus:outline-none focus:border-[#FF6B35]">
            @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-[#FF6B35] text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition mt-4">
            Đăng Ký
        </button>
    </form>

    <p class="text-center mt-6 text-gray-600">
        Đã có tài khoản? <a href="{{ route('login') }}" class="text-[#FF6B35] font-bold hover:underline">Đăng nhập</a>
    </p>
</div>
@endsection