@extends('layouts.app')

@section('title', 'Hồ sơ của tôi - SunFlower')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-[#FF6B35] px-8 py-6 text-white text-center">
            <h2 class="text-2xl font-bold font-serif">Thông tin tài khoản</h2>
            <p class="text-orange-100 text-sm mt-1">Quản lý thông tin cá nhân của bạn để nhận dịch vụ tốt nhất</p>
        </div>

        @if(session('success'))
            <div class="m-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500 uppercase tracking-wider">Mã khách hàng</label>
                    <input type="text" value="{{ $user->makh }}" disabled class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-gray-400 cursor-not-allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 uppercase tracking-wider">Địa chỉ Email</label>
                    <input type="email" value="{{ $user->email }}" disabled class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-md text-gray-400 cursor-not-allowed">
                </div>

                <div>
                    <label for="hoten" class="block text-sm font-medium text-gray-700">Họ và tên</label>
                    <input type="text" id="hoten" name="hoten" value="{{ old('hoten', $user->hoten) }}" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-[#FF6B35] focus:border-[#FF6B35] transition-colors">
                    @error('hoten') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="sdt" class="block text-sm font-medium text-gray-700">Số điện thoại</label>
                    <input type="text" id="sdt" name="sdt" value="{{ old('sdt', $user->sdt) }}" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-[#FF6B35] focus:border-[#FF6B35] transition-colors">
                    @error('sdt') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="diachi" class="block text-sm font-medium text-gray-700">Địa chỉ giao hàng mặc định</label>
                <input type="text" id="diachi" name="diachi" value="{{ old('diachi', $user->diachi) }}" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-[#FF6B35] focus:border-[#FF6B35] transition-colors" placeholder="Ví dụ: 123 Đường Hoa, Quận 1, TP.HCM">
                @error('diachi') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="pt-4 flex justify-end gap-4 border-t border-gray-100">
                <button type="submit" class="bg-[#FF6B35] hover:bg-[#e85a25] text-white font-bold py-3 px-8 rounded-md shadow-lg shadow-orange-500/30 transition-all transform active:scale-95">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection