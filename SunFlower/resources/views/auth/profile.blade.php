@extends('layouts.app')

@section('title', 'Thông tin cá nhân - SunFlower')

@section('content')
<style>
    body, html, #app, main {
        background-color: #FCFBEE !important;
    }
</style>
<div class=" min-h-screen py-10 font-sans text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-100 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="font-medium text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <!-- 1. KHU VỰC HEADER TÀI KHOẢN -->
        <div class="bg-white rounded-[2rem] p-6 lg:p-8 shadow-sm border border-gray-100 mb-8 flex flex-col xl:flex-row items-center justify-between gap-8">
            
            <!-- Left: Avatar & Info -->
            @php
                $tierName = $user->hangThanhVien ? $user->hangThanhVien->ten_hang : 'Mới';
                
                // Cài đặt màu sắc theo Hạng
                if ($tierName == 'Hạng Bạc') {
                    $avatarGradient = 'from-slate-300 to-slate-400';
                    $badgeColor = 'bg-slate-400';
                } elseif ($tierName == 'Hạng Vàng') {
                    $avatarGradient = 'from-yellow-400 to-yellow-500';
                    $badgeColor = 'bg-yellow-500';
                } elseif ($tierName == 'Hạng Kim Cương') {
                    $avatarGradient = 'from-blue-400 to-blue-500';
                    $badgeColor = 'bg-blue-500';
                } else {
                    // Hạng Đồng hoặc Khách hàng mới (Màu cam SunFlower)
                    $avatarGradient = 'from-[#FFB266] to-[#FF7F00]';
                    $badgeColor = 'bg-[#FF7F00]';
                }
            @endphp
            
            <div class="flex items-center gap-5 w-full xl:w-auto">
                <!-- Avatar box -->
                <div class="relative">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br {{ $avatarGradient }} flex items-center justify-center text-white text-2xl font-bold shadow-inner">
                        {{ strtoupper(substr($user->hoten, 0, 1)) }}
                    </div>
                    <!-- Star Badge -->
                    <div class="absolute -bottom-2 -right-2 bg-white rounded-full p-1 shadow-sm">
                        <div class="{{ $badgeColor }} text-white rounded-full p-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $user->hoten }}</h2>
                    <div class="flex items-center gap-2">
                        <!-- Nhãn Hạng thành viên cũng tự động đổi màu -->
                        <span class="{{ $badgeColor }} text-white px-3 py-1 rounded-full text-xs font-semibold">
                            {{ $tierName }}
                        </span>
                        <span class="text-gray-400 text-sm font-medium">• {{ $user->makh }}</span>
                    </div>
                </div>
            </div>

            <!-- Middle: Progress Bar -->
            <div class="w-full xl:flex-1 max-w-2xl px-0 xl:px-8">
                <div class="flex justify-between text-sm font-medium text-gray-500 mb-2">
                    <span>{{ $nextTier ? 'Lên hạng ' . $nextTier->ten_hang : 'Đã đạt hạng tối đa' }}</span>
                    <span>{{ number_format($percent, 0) }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 mb-2">
                    <div class="bg-slate-500 h-3 rounded-full transition-all duration-1000" style="width: {{ $percent }}%"></div>
                </div>
                <div class="flex justify-between text-xs font-medium">
                    <span class="text-gray-500">{{ number_format($user->tong_chi_tieu) }}đ</span>
                    @if($nextTier)
                        <span class="text-[#FF7F00]">Còn {{ number_format($nextTier->chi_tieu_toi_thieu - $user->tong_chi_tieu) }}đ &rarr;</span>
                    @endif
                </div>
            </div>

            <!-- Right: Points Card -->
            <div class="w-full xl:w-auto bg-gradient-to-br from-[#FF8A00] to-[#FF6B00] rounded-3xl p-6 text-white text-center min-w-[240px] shadow-lg shadow-orange-500/20">
                <div class="flex items-center justify-center gap-1 mb-2 text-white/90">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd"></path></svg>
                    <span class="text-xs font-bold uppercase tracking-wider">Điểm thưởng</span>
                </div>
                <div class="text-4xl font-extrabold mb-4 leading-none">{{ number_format($user->diem_thuong) }}</div>
                <button id="btn-open-modal" class="w-full bg-white text-[#FF7F00] font-bold text-sm py-2.5 rounded-full hover:bg-orange-50 transition-colors">
                    Đổi điểm ngay
                </button>
            </div>
        </div>

        <!-- 2. KHU VỰC GRID (FORM & VOUCHER) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- CỘT TRÁI: FORM THÔNG TIN -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100">
                    <h3 class="text-xl font-bold text-gray-800 mb-8">Thông tin cá nhân</h3>
                    
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Mã KH -->
                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Mã khách hàng
                                </label>
                                <input type="text" value="{{ $user->makh }}" disabled class="w-full bg-[#FAFAFA] border-none text-gray-400 text-sm rounded-2xl px-5 py-3.5 focus:ring-0 cursor-not-allowed">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    Email
                                </label>
                                <input type="email" value="{{ $user->email }}" disabled class="w-full bg-[#FAFAFA] border-none text-gray-400 text-sm rounded-2xl px-5 py-3.5 focus:ring-0 cursor-not-allowed">
                            </div>

                            <!-- Họ tên -->
                            <div>
                                <label for="hoten" class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Họ và tên
                                </label>
                                <input type="text" id="hoten" name="hoten" value="{{ old('hoten', $user->hoten) }}" class="w-full bg-[#FAFAFA] border border-transparent focus:border-gray-200 focus:bg-white text-gray-700 text-sm rounded-2xl px-5 py-3.5 outline-none transition-all">
                                @error('hoten') <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Số điện thoại -->
                            <div>
                                <label for="sdt" class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    Số điện thoại
                                </label>
                                <input type="text" id="sdt" name="sdt" value="{{ old('sdt', $user->sdt) }}" class="w-full bg-[#FAFAFA] border border-transparent focus:border-gray-200 focus:bg-white text-gray-700 text-sm rounded-2xl px-5 py-3.5 outline-none transition-all">
                                @error('sdt') <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Địa chỉ -->
                        <div>
                            <label for="diachi" class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 ml-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Địa chỉ giao hàng
                            </label>
                            <input type="text" id="diachi" name="diachi" value="{{ old('diachi', $user->diachi) }}" class="w-full bg-[#FAFAFA] border border-transparent focus:border-gray-200 focus:bg-white text-gray-700 text-sm rounded-2xl px-5 py-3.5 outline-none transition-all">
                            @error('diachi') <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-[#111827] text-white font-medium text-sm px-8 py-3 rounded-full shadow-md hover:bg-black transition-colors">
                                Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- CỘT PHẢI: KHO VOUCHER -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-[2rem] p-8 shadow-sm border border-gray-100 h-full flex flex-col">
                    <div class="flex justify-between items-center mb-8">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#FF7F00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            Kho Voucher
                        </h3>
                        <span class="bg-[#FF7F00] text-white text-[11px] font-semibold px-3 py-1.5 rounded-full">
                            {{ $user->vouchers->where('pivot.trang_thai', 0)->count() }} khả dụng
                        </span>
                    </div>

                    <!-- Danh sách Scroll -->
                    <div class="overflow-y-auto pr-2 space-y-4 max-h-[450px] scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                        
                        @forelse($user->vouchers as $voucher)
                            <!-- Thẻ Voucher -->
                            <div class="bg-[#FFF9F5] border border-orange-100 rounded-2xl p-5 relative">
                                <h4 class="font-bold text-gray-800 mb-1">{{ $voucher->tenvoucher }}</h4>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-4">
                                    <svg class="w-4 h-4 text-[#FF7F00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                    <span class="font-semibold text-[#FF7F00]">{{ $voucher->gia_tri_giam }}{{ $voucher->loai_giam == 'phan_tram' ? '%' : 'đ' }}</span>
                                    <span>•</span>
                                    <span>Tối thiểu {{ number_format($voucher->don_min) }}đ</span>
                                </div>

                                <div class="flex items-center gap-3">
                                    <div class="flex-1 border-2 border-dashed border-[#FFB266] rounded-full py-2.5 px-4 text-center">
                                        <span class="text-[#FF7F00] font-bold tracking-widest text-sm">{{ $voucher->mavoucher }}</span>
                                    </div>
                                    <button class="w-12 h-12 bg-[#FF7F00] rounded-xl flex items-center justify-center text-white hover:bg-orange-600 transition-colors shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    </button>
                                </div>

                                <div class="mt-4 flex items-center gap-1.5 text-[11px] text-gray-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span>Ngày nhận: {{ \Carbon\Carbon::parse($voucher->pivot->ngay_doi)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-gray-400">
                                <p class="text-sm">Bạn chưa có voucher nào.</p>
                            </div>
                        @endforelse
                        
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div id="voucher-modal" class="fixed inset-0 z-[100] hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/30 backdrop-blur-sm" onclick="document.getElementById('voucher-modal').classList.add('hidden')"></div>

    <div class="bg-white rounded-[2rem] w-full max-w-lg shadow-2xl p-8 relative animate-in fade-in zoom-in duration-300">
        <button onclick="document.getElementById('voucher-modal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-800 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Kho ưu đãi đặc quyền</h3>
            <p class="text-gray-400 text-sm">Dùng điểm thưởng để đổi lấy mã giảm giá tốt nhất cho bạn</p>
        </div>
        
        <div class="space-y-4 max-h-[50vh] overflow-y-auto pr-2 custom-scrollbar">
            @php 
                $vouchers = \App\Models\Voucher::where('diem_doi', '>', 0)
                            ->where('soluong', '>', \DB::raw('da_sudung'))
                            ->where('trangthai', 1)
                            ->get(); 
            @endphp

            @forelse($vouchers as $v)
                <div class="flex items-center justify-between p-5 border border-gray-100 rounded-3xl bg-[#FFF9F5] hover:border-orange-200 transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-[#FF7F00] shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5A2 2 0 1010 7v1m3 1l9 9-4 4-9-9V9z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800">{{ $v->tenvoucher }}</p>
                            <span class="inline-flex items-center gap-1 bg-white px-2 py-0.5 rounded-lg border border-orange-100 text-xs font-semibold text-[#FF7F00]">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                {{ number_format($v->diem_doi) }} điểm
                            </span>
                        </div>
                    </div>
                    
                    <form action="{{ route('profile.exchange_voucher') }}" method="POST">
                        @csrf
                        <input type="hidden" name="mavoucher" value="{{ $v->mavoucher }}">
                        <button type="submit" class="bg-gray-800 text-white font-bold text-sm px-5 py-2.5 rounded-2xl hover:bg-black transition-transform active:scale-95">
                            Đổi ngay
                        </button>
                    </form>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-gray-400 text-sm">Hiện chưa có voucher nào khả dụng để đổi điểm.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    /* CSS cho scrollbar mượt mà */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnOpen = document.getElementById('btn-open-modal');
        const modal = document.getElementById('voucher-modal');
        const btnClose = modal.querySelector('button'); // Nút đóng trong modal

        if (btnOpen && modal) {
            btnOpen.addEventListener('click', function() {
                modal.classList.remove('hidden');
                console.log('Đã mở modal'); // Kiểm tra trong F12 Console
            });

            btnClose.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            // Đóng khi click ra ngoài vùng modal
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        } else {
            console.error('Không tìm thấy nút hoặc modal, hãy kiểm tra lại ID!');
        }
    });
</script>
@endsection