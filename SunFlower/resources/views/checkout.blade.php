@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Xác nhận thanh toán</h1>

    <form action="{{ route('order.place') }}" method="POST">
        @csrf
        <div class="flex flex-col lg:flex-row gap-12">
            
            <div class="w-full lg:w-7/12">
                <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Thông tin nhận hàng
                    </h2>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Họ và tên</label>
                                <input type="text" name="ten_nguoinhan" required 
                                       value="{{ Auth::guard('khachhang')->user()->tenkh ?? '' }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                                <input type="text" name="sdt_nguoinhan" required 
                                       value="{{ Auth::guard('khachhang')->user()->sdt ?? '' }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ giao hàng</label>
                            <textarea name="diachi_giaohang" rows="3" required
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none"
                                      placeholder="Số nhà, tên đường, phường/xã, quận/huyện...">{{ Auth::guard('khachhang')->user()->diachi ?? '' }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea name="ghichu" rows="2"
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none"
                                      placeholder="Ví dụ: Giao vào giờ hành chính, gọi trước khi đến..."></textarea>
                        </div>
                    </div>

                    <h2 class="text-xl font-bold mt-10 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Phương thức thanh toán
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-orange-50/50 transition has-[:checked]:border-[#FF6B35] has-[:checked]:bg-orange-50/30">
                            <input type="radio" name="phuongthuc_thanhtoan" value="cod" checked class="w-5 h-5 text-[#FF6B35] focus:ring-[#FF6B35]">
                            <div class="ml-4">
                                <span class="block font-bold text-gray-900">Thanh toán khi nhận hàng (COD)</span>
                            </div>
                        </label>
                        <label class="relative flex items-center p-4 border border-gray-100 rounded-2xl cursor-pointer hover:bg-orange-50/50 transition has-[:checked]:border-[#FF6B35] has-[:checked]:bg-orange-50/30">
                            <input type="radio" name="phuongthuc_thanhtoan" value="vnpay" class="w-5 h-5 text-[#FF6B35] focus:ring-[#FF6B35]">
                            <div class="ml-4">
                                <span class="block font-bold text-gray-900">Thanh toán qua VNPay / MoMo</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-5/12">
                <div class="bg-gray-50 rounded-3xl p-8 sticky top-24 border border-gray-100">
                    <h2 class="text-xl font-bold mb-6 text-gray-900">Đơn hàng của bạn</h2>
                    
                    <div class="space-y-4 mb-8 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @php $finalTotal = 0; @endphp
                        @foreach($checkoutItems as $id => $item)
                            @php $finalTotal += $item['price'] * $item['quantity']; @endphp
                            <div class="flex items-center gap-4 bg-white p-4 rounded-2xl">
                                <img src="{{ route('product.image', $id) }}" class="w-16 h-16 rounded-xl object-cover">
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm text-gray-900 line-clamp-1">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-gray-500">Số lượng: {{ $item['quantity'] }}</p>
                                </div>
                                <span class="font-bold text-sm text-gray-900">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} ₫
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-4 border-t border-gray-200 pt-6">
                        <div class="flex justify-between text-gray-600">
                            <span>Tạm tính</span>
                            <span class="font-medium text-gray-900">{{ number_format($finalTotal, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Phí vận chuyển</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>
                        <div class="flex justify-between items-center pt-4">
                            <span class="text-lg font-bold text-gray-900">Tổng thanh toán</span>
                            <span class="text-2xl font-extrabold text-[#FF6B35]">{{ number_format($finalTotal, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#FF6B35] hover:bg-orange-600 text-white py-4 rounded-2xl font-bold text-lg mt-8 shadow-lg shadow-orange-100 transition active:scale-[0.98]">
                        Đặt hàng ngay
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4 px-6 italic">
                        Bằng việc nhấn đặt hàng, bạn đồng ý với các điều khoản dịch vụ của SunFlower.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection