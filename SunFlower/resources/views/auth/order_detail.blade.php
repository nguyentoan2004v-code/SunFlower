@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng ' . $donHang->madon)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    
    {{-- Khối hiển thị thông báo trả về từ Controller (Thành công / Lỗi) --}}
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-2xl flex items-center gap-2 shadow-sm animate-bounce-short">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-2xl flex items-center gap-2 shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <a href="{{ route('orders.history') }}" class="text-sm text-gray-500 hover:text-[#FF6B35] flex items-center gap-1 mb-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Quay lại lịch sử
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Chi tiết đơn hàng #{{ $donHang->madon }}</h1>
        </div>
        
        @if($donHang->trangthai == 'Chờ xác nhận')
            <form action="{{ route('orders.cancel', $donHang->madon) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                @csrf
                <button type="submit" class="bg-white border-2 border-red-500 text-red-500 hover:bg-red-50 font-bold py-2.5 px-6 rounded-2xl transition shadow-sm">
                    Hủy đơn hàng
                </button>
            </form>
        @endif
    </div>

    {{-- THANH TRẠNG THÁI ĐƠN HÀNG --}}
    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm mb-8">
        @php
            $statuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao'];
            $dbStatus = $donHang->trangthai;
            $currentIndex = 0; 
            if ($dbStatus == 'Đang giao') $currentIndex = 2; 
            elseif ($dbStatus == 'Đã hoàn thành') $currentIndex = 3; 
            $isCancelled = ($dbStatus == 'Đã hủy');
        @endphp

        @if($isCancelled)
            <div class="flex flex-col items-center justify-center gap-3 text-red-500 font-bold py-6 bg-red-50/50 rounded-3xl">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-lg">ĐƠN HÀNG ĐÃ BỊ HỦY</span>
            </div>
        @else
            <div class="relative flex justify-between items-center w-full mt-4 mb-2">
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1.5 bg-gray-100 w-full rounded-full"></div>
                <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1.5 bg-gradient-to-r from-orange-400 to-[#FF6B35] transition-all duration-700 rounded-full" 
                     style="width: {{ $currentIndex * 33.33 }}%"></div>

                @foreach($statuses as $index => $label)
                    @php
                        $colorClass = 'bg-gray-100 text-gray-400 border-gray-200';
                        if ($index <= $currentIndex) {
                            $colorClass = match($label) {
                                'Chờ xác nhận' => 'bg-yellow-400 text-white border-yellow-100',
                                'Đã xác nhận'  => 'bg-blue-500 text-white border-blue-100',
                                'Đang giao'    => 'bg-purple-500 text-white border-purple-100',
                                'Đã giao'      => 'bg-[#FF6B35] text-white border-orange-100',
                                default        => 'bg-gray-100'
                            };
                        }
                    @endphp
                    <div class="relative z-10 flex flex-col items-center group">
                       <div class="w-12 h-12 {{ $colorClass }} rounded-full flex items-center justify-center shadow-sm transition-all duration-500 border-4">
                            @if($index <= $currentIndex)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            @else
                                <span class="font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                        <span class="absolute top-14 text-xs font-bold whitespace-nowrap {{ $index <= $currentIndex ? 'text-gray-900' : 'text-gray-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- THÔNG TIN ĐƠN HÀNG --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Thông tin nhận hàng --}}
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm h-full hover:shadow-md transition-shadow">
            <h3 class="font-black text-xl mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-orange-100 text-[#FF6B35] flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                </span>
                Thông tin nhận hàng
            </h3>
            <div class="space-y-5 text-gray-600">
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Số điện thoại</span>
                    <span class="font-bold text-gray-900 text-lg">{{ $donHang->sdt_nhan }}</span>
                </div>
                <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Địa chỉ giao hàng</span>
                    <span class="font-medium text-gray-900 leading-relaxed">{{ $donHang->diachi_giao }}</span>
                </div>
                @if($donHang->ghichu)
                <div class="bg-yellow-50 rounded-2xl p-4 border border-yellow-100">
                    <span class="block text-xs font-bold text-yellow-600 uppercase tracking-wider mb-1">Ghi chú</span>
                    <span class="font-medium text-gray-900">{{ $donHang->ghichu }}</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Sản phẩm đã đặt --}}
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm h-full hover:shadow-md transition-shadow flex flex-col">
            <h3 class="font-black text-xl mb-6 flex items-center gap-2">
                <span class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </span>
                Sản phẩm đã đặt
            </h3>
            <div class="space-y-4 flex-1">
                @foreach($donHang->sanphams as $sp)
                    <div class="flex items-center gap-4 bg-gray-50/50 p-3 rounded-2xl border border-gray-50">
                        @php
                            $spKhImg = asset('images/bg-sunflower.jpg');
                            if(!empty($sp->hinhanh)){
                                $spKhImg = str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : asset('storage/' . ltrim($sp->hinhanh, '/'));
                            }
                        @endphp
                        <img src="{{ $spKhImg }}" class="w-16 h-16 object-cover rounded-xl shadow-sm">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 line-clamp-1">{{ $sp->tensp }}</p>
                            <p class="text-sm text-gray-500 font-medium mt-0.5">Số lượng: x{{ $sp->pivot->soluong }}</p>
                        </div>
                        <p class="font-bold text-gray-900">{{ number_format($sp->pivot->giaban, 0, ',', '.') }} ₫</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 pt-6 border-t-2 border-dashed border-gray-200 flex justify-between items-end">
                <span class="font-bold text-gray-500 uppercase tracking-wide text-sm">Tổng thanh toán</span>
                <span class="text-3xl font-black text-[#FF6B35]">{{ number_format($donHang->tongtien, 0, ',', '.') }} ₫</span>
            </div>
        </div>
    </div>

    {{-- KHU VỰC ĐÁNH GIÁ SẢN PHẨM (Thiết kế mới lạ, thân thiện) --}}
    @if($donHang->trangthai == 'Đã hoàn thành')
        <div class="relative bg-gradient-to-br from-white to-orange-50/30 rounded-[2.5rem] p-8 md:p-12 border border-orange-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
            
            {{-- Đồ họa trang trí góc nền --}}
            <div class="absolute -top-16 -right-16 w-64 h-64 bg-gradient-to-br from-orange-200/40 to-yellow-100/40 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-gradient-to-tr from-orange-200/30 to-pink-100/30 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 text-center mb-10">
                <h2 class="text-3xl font-black text-gray-900 mb-3 tracking-tight">Bạn cảm thấy thế nào?</h2>
                <p class="text-gray-500 font-medium">Đánh giá của bạn sẽ giúp chúng tôi hoàn thiện hơn mỗi ngày 🌻</p>
            </div>
            
            <div class="relative z-10 space-y-8 max-w-2xl mx-auto">
                @foreach($donHang->sanphams as $sp)
                    <div class="bg-white rounded-[2rem] p-6 md:p-8 shadow-sm border border-gray-100 hover:shadow-lg hover:border-orange-200 transition-all duration-300">
                        
                        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-50">
                            @php
                                $spKhImg = asset('images/bg-sunflower.jpg');
                                if(!empty($sp->hinhanh)){
                                    $spKhImg = str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : asset('storage/' . ltrim($sp->hinhanh, '/'));
                                }
                            @endphp
                            <img src="{{ $spKhImg }}" class="w-14 h-14 object-cover rounded-2xl shadow-sm">
                            <div>
                                <p class="font-bold text-gray-800 text-lg">{{ $sp->tensp }}</p>
                                <p class="text-xs font-bold text-gray-400 uppercase mt-1">Đơn hàng ngày: {{ date('d/m/Y', strtotime($donHang->ngaydat)) }}</p>
                            </div>
                        </div>
                        
                        <form action="{{ route('danhgia.store') }}" method="POST" class="flex flex-col">
                            @csrf
                            <input type="hidden" name="madon" value="{{ $donHang->madon }}">
                            <input type="hidden" name="masp" value="{{ $sp->masp }}">
                            
                            {{-- Trải nghiệm chọn sao tập trung ở giữa --}}
                            <div class="flex flex-col items-center justify-center mb-8">
                                <div class="flex items-center gap-1.5 star-rating cursor-pointer" data-id="{{ $sp->masp }}">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-10 h-10 md:w-12 md:h-12 star-icon text-gray-200 hover:scale-125 transition-transform duration-200" data-value="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                    <input type="hidden" name="so_sao" class="rating-input" value="5" required>
                                </div>
                                {{-- Nút badge trạng thái động --}}
                                <div class="mt-4 px-4 py-1.5 rounded-full font-bold text-sm transition-colors duration-300 bg-orange-100 text-[#FF6B35] rating-badge">
                                    Tuyệt vời 😍
                                </div>
                            </div>
                            
                            {{-- Ô nhập liệu Soft UI --}}
                            <div class="relative mb-6">
                                <textarea name="binh_luan" rows="3" placeholder="Sản phẩm có làm bạn hài lòng không? Hãy chia sẻ thêm nhé (không bắt buộc)..." class="w-full bg-gray-50 border-transparent focus:bg-white focus:border-orange-300 focus:ring-4 focus:ring-orange-100/50 rounded-[1.5rem] p-5 text-sm font-medium text-gray-700 transition-all resize-none shadow-inner placeholder-gray-400"></textarea>
                            </div>
                            
                            <button type="submit" class="w-full bg-gray-900 text-white font-bold px-6 py-4 rounded-[1.5rem] hover:bg-[#FF6B35] hover:shadow-lg hover:shadow-orange-200 transition-all duration-300 flex items-center justify-center gap-2 group">
                                <span class="text-[15px]">Gửi đánh giá ngay</span>
                                <svg class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Script xử lý UI Đánh Giá Siêu Mượt --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starRatings = document.querySelectorAll('.star-rating');
        
        // Cấu hình Emoji và màu sắc cho từng mức sao
        const ratingConfig = {
            1: { text: 'Rất tệ 😞', class: 'bg-red-100 text-red-600' },
            2: { text: 'Tạm được 😐', class: 'bg-orange-50 text-orange-500' },
            3: { text: 'Bình thường 🙂', class: 'bg-blue-50 text-blue-500' },
            4: { text: 'Rất tốt 😃', class: 'bg-green-50 text-green-600' },
            5: { text: 'Tuyệt vời 😍', class: 'bg-orange-100 text-[#FF6B35]' }
        };

        starRatings.forEach(container => {
            const stars = container.querySelectorAll('.star-icon');
            const input = container.querySelector('.rating-input');
            const badge = container.nextElementSibling; 
            
            function updateUI(val) {
                // Cập nhật Sao
                stars.forEach(star => {
                    const starValue = star.getAttribute('data-value');
                    if (starValue <= val) {
                        star.classList.remove('text-gray-200', 'scale-100');
                        star.classList.add('text-yellow-400', 'scale-110');
                    } else {
                        star.classList.remove('text-yellow-400', 'scale-110');
                        star.classList.add('text-gray-200', 'scale-100');
                    }
                });

                // Cập nhật Badge chữ & màu sắc
                const config = ratingConfig[val];
                badge.textContent = config.text;
                // Reset classes
                badge.className = `mt-4 px-4 py-1.5 rounded-full font-bold text-sm transition-colors duration-300 rating-badge ${config.class}`;
            }

            // Gọi lần đầu để set 5 sao
            updateUI(5);

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    input.value = value;
                    updateUI(value);
                });

                star.addEventListener('mouseenter', function() {
                    updateUI(this.getAttribute('data-value'));
                });

                star.addEventListener('mouseleave', function() {
                    updateUI(input.value);
                });
            });
        });
    });
</script>
@endsection