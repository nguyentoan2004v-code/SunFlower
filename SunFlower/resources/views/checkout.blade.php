@extends('layouts.app')

@section('title', 'Thanh toán đơn hàng - SunFlower')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-extrabold text-gray-900 mb-8">Xác nhận thanh toán</h1>

    <form action="{{ route('order.place') }}" method="POST" data-loading>
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
                                       value="{{ old('ten_nguoinhan', Auth::guard('khachhang')->user()->hoten ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none @error('ten_nguoinhan') border-red-400 @enderror">
                                @error('ten_nguoinhan')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Số điện thoại</label>
                                <input type="text" name="sdt_nguoinhan" required 
                                       value="{{ old('sdt_nguoinhan', Auth::guard('khachhang')->user()->sdt ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none @error('sdt_nguoinhan') border-red-400 @enderror">
                                @error('sdt_nguoinhan')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- ĐỊA CHỈ GIAO HÀNG - CASCADING DROPDOWN --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-3">Địa chỉ giao hàng</label>
                            
                            {{-- Hidden field to store the final concatenated address --}}
                            <input type="hidden" name="diachi_giaohang" id="diachi_giaohang_hidden" 
                                   value="{{ old('diachi_giaohang', Auth::guard('khachhang')->user()->diachi ?? '') }}">

                            <div class="space-y-3">
                                {{-- Row 1: Province & District --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    {{-- Tỉnh / Thành phố --}}
                                    <div class="relative">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Tỉnh / Thành phố
                                            </span>
                                        </label>
                                        <select id="addr_province" 
                                                class="address-select w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none appearance-none cursor-pointer transition-all hover:border-gray-300 text-gray-700 text-sm">
                                            <option value="" disabled selected>-- Chọn Tỉnh/Thành phố --</option>
                                        </select>
                                        <div class="pointer-events-none absolute right-3 bottom-3.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>

                                    {{-- Quận / Huyện --}}
                                    <div class="relative">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">
                                            <span class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                Quận / Huyện
                                            </span>
                                        </label>
                                        <select id="addr_district" 
                                                class="address-select w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none appearance-none cursor-pointer transition-all hover:border-gray-300 text-gray-700 text-sm disabled:bg-gray-50 disabled:cursor-not-allowed disabled:text-gray-400"
                                                disabled>
                                            <option value="" disabled selected>-- Chọn Quận/Huyện --</option>
                                        </select>
                                        <div class="pointer-events-none absolute right-3 bottom-3.5">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Row 2: Ward --}}
                                <div class="relative">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Phường / Xã
                                        </span>
                                    </label>
                                    <select id="addr_ward" 
                                            class="address-select w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none appearance-none cursor-pointer transition-all hover:border-gray-300 text-gray-700 text-sm disabled:bg-gray-50 disabled:cursor-not-allowed disabled:text-gray-400"
                                            disabled>
                                        <option value="" disabled selected>-- Chọn Phường/Xã --</option>
                                    </select>
                                    <div class="pointer-events-none absolute right-3 bottom-3.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                </div>

                                {{-- Row 3: Detail Address --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                            Số nhà, tên đường
                                        </span>
                                    </label>
                                    <input type="text" id="addr_detail"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-white focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none transition-all hover:border-gray-300 text-sm"
                                           placeholder="VD: 123 Nguyễn Văn A, Tổ 5">
                                </div>

                                {{-- Address Preview --}}
                                <div id="address_preview" class="hidden">
                                    <div class="flex items-start gap-3 bg-gradient-to-r from-orange-50 to-amber-50 border border-orange-100 rounded-xl p-3.5 transition-all">
                                        <div class="shrink-0 mt-0.5">
                                            <div class="w-8 h-8 bg-[#FF6B35] rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-[10px] font-bold text-orange-600 uppercase tracking-wider mb-1">Địa chỉ giao hàng</p>
                                            <p id="address_preview_text" class="text-sm font-medium text-gray-800 leading-relaxed break-words"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @error('diachi_giaohang')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
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
                                @php
                                    $chkProduct = \App\Models\SanPham::find($id);
                                    $chkImg = asset('images/bg-sunflower.jpg');
                                    if($chkProduct && !empty($chkProduct->hinhanh)){
                                        $chkImg = str_starts_with($chkProduct->hinhanh, 'http') ? $chkProduct->hinhanh : asset('storage/' . ltrim($chkProduct->hinhanh, '/'));
                                    }
                                @endphp
                                <img src="{{ $chkImg }}" class="w-16 h-16 rounded-xl object-cover">
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

                    <div class="border-t border-gray-200 pt-6 pb-2 mt-4">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-2 text-gray-700 font-bold text-lg">
                                <svg class="w-6 h-6 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                SunFlower Voucher
                            </div>
                            
                            @if(session()->has('voucher'))
                                <div class="flex items-center gap-3">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold border border-green-200">
                                        Đã giảm {{ number_format(session('voucher')['tien_giam'], 0, ',', '.') }} ₫
                                    </span>
                                    <button type="button" onclick="document.getElementById('form-go-voucher').submit();" class="text-red-500 hover:text-red-700 text-sm font-medium hover:underline">Gỡ bỏ</button>
                                </div>
                            @else
                                <button type="button" onclick="openVoucherModal()" class="text-blue-600 hover:text-blue-800 font-semibold transition flex items-center gap-1">
                                    Chọn hoặc nhập mã <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    @php
                        $tamtinh = $finalTotal ?? 0;
                        $tienGiam = session()->has('voucher') ? session('voucher')['tien_giam'] : 0;
                        $tongThanhToan = max(0, $tamtinh - $tienGiam);
                    @endphp

                    @php
                        // 1. Tính tổng tiền hàng gốc từ session
                        $tongTienHang = 0;
                        foreach ($checkoutItems as $item) {
                            $tongTienHang += $item['price'] * $item['quantity'];
                        }

                        // 2. Lấy số tiền giảm từ Voucher trong session (nếu có)
                        $tienGiamVoucher = session()->has('voucher') ? session('voucher')['tien_giam'] : 0;

                        // 3. Tính số tiền giảm theo Hạng thành viên của tài khoản đang đăng nhập
                        $tienGiamTheoHang = 0;
                        $tenHang = '';
                        if (Auth::guard('khachhang')->check()) {
                            $user = Auth::guard('khachhang')->user()->load('hangThanhVien');
                            if ($user->hangThanhVien && $user->hangThanhVien->phan_tram_giam > 0) {
                                $tenHang = $user->hangThanhVien->ten_hang;
                                $tienGiamTheoHang = $tongTienHang * ($user->hangThanhVien->phan_tram_giam / 100);
                            }
                        }

                        // 4. Tính Tổng thanh toán cuối cùng
                        $tongThanhToanCuoiCung = max(0, $tongTienHang - $tienGiamVoucher - $tienGiamTheoHang);
                    @endphp
                    <div class="space-y-3 border-t border-gray-100 pt-4">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tạm tính</span>
                            <span class="font-medium text-gray-950">{{ number_format($tongTienHang, 0, ',', '.') }} đ</span>
                        </div>

                        @if($tienGiamVoucher > 0)
                            <div class="flex justify-between text-sm text-red-600">
                                <span>Voucher giảm giá</span>
                                <span class="font-medium">- {{ number_format($tienGiamVoucher, 0, ',', '.') }} đ</span>
                            </div>
                        @endif

                        @if($tienGiamTheoHang > 0)
                            <div class="flex justify-between text-sm text-orange-600">
                                <span>Ưu đãi ({{ $tenHang }})</span>
                                <span class="font-medium">- {{ number_format($tienGiamTheoHang, 0, ',', '.') }} đ</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Phí vận chuyển</span>
                            <span class="text-green-600 font-medium">Miễn phí</span>
                        </div>

                        <div class="flex justify-between items-center border-t border-gray-100 pt-4 mt-2">
                            <span class="text-base font-bold text-gray-900">Tổng thanh toán</span>
                            <span class="text-2xl font-extrabold text-[#FF6B35]">
                                {{ number_format($tongThanhToanCuoiCung, 0, ',', '.') }} đ
                            </span>
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
    <div id="voucherModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-60 flex items-center justify-center transition-opacity backdrop-blur-sm">
        <div class="bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden shadow-2xl flex flex-col max-h-[85vh] animate-[slideIn_0.3s_ease-out]">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-extrabold text-gray-900">Chọn SunFlower Voucher</h3>
                <button type="button" onclick="closeVoucherModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1 bg-[#f8f9fa]">
                
                <form action="{{ route('voucher.apply') }}" method="POST" class="flex gap-2 mb-8">
                    @csrf
                    <input type="text" name="mavoucher" placeholder="Nhập mã voucher (nếu có)" class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#FF6B35] focus:border-[#FF6B35] outline-none uppercase font-medium bg-white shadow-sm" required>
                    <button type="submit" class="bg-gray-200 hover:bg-[#FF6B35] hover:text-white text-gray-700 font-bold px-6 py-3 rounded-lg transition">ÁP DỤNG</button>
                </form>

                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Mã miễn phí có sẵn cho bạn</h4>
                
               <div class="space-y-4">
                    @forelse($publicVouchers as $vc)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex items-center justify-between hover:border-[#FF6B35] hover:shadow-md transition">
                            <div class="flex items-start gap-4">
                                <div class="bg-orange-50 p-3 rounded-full text-[#FF6B35] mt-1">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-sm mb-2">{{ $vc->tenvoucher }}</div>
                                    <div class="font-extrabold text-[#FF6B35] text-xl leading-none mb-1.5">
                                        @if($vc->loai_giam === 'phan_tram')
                                            Giảm {{ (int)$vc->gia_tri_giam }}%
                                        @else
                                            Giảm {{ number_format($vc->gia_tri_giam, 0, ',', '.') }}đ
                                        @endif
                                    </div>
                                    
                                    
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div>• Đơn tối thiểu: <span class="font-semibold text-gray-900">{{ number_format($vc->don_min, 0, ',', '.') }}đ</span></div>
                                        
                                        @if($vc->loai_giam === 'phan_tram' && $vc->giam_max)
                                            <div>• Giảm tối đa: <span class="font-semibold text-gray-900">{{ number_format($vc->giam_max, 0, ',', '.') }}đ</span></div>
                                        @endif
                                        
                                        @if($vc->loai_ap_dung === 'danh_muc')
                                            <div class="text-[#FF6B35] font-medium text-xs bg-orange-50 inline-block px-2 py-0.5 rounded border border-orange-100">
                                                * Chỉ áp dụng một số danh mục
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="text-xs text-gray-500 mt-3 font-medium bg-gray-100 inline-block px-2 py-1 rounded">
                                        HSD: {{ date('d/m/Y H:i', strtotime($vc->ngay_kt)) }}
                                    </div>
                                </div>
                            </div>
                            <form action="{{ route('voucher.apply') }}" method="POST" class="ml-2 shrink-0">
                                @csrf
                                <input type="hidden" name="mavoucher" value="{{ $vc->mavoucher }}">
                                <button type="submit" class="bg-[#FF6B35] text-white text-sm font-bold px-4 py-2.5 rounded-lg hover:bg-orange-600 transition shadow-sm active:scale-95 whitespace-nowrap">
                                    Dùng ngay
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="text-center text-gray-400 py-8 bg-white rounded-xl border border-dashed border-gray-300">
                            <i class="fa-solid fa-ticket fa-2x mb-2 text-gray-300"></i><br>
                            Hiện tại không có mã giảm giá nào.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        function openVoucherModal() {
            document.getElementById('voucherModal').classList.remove('hidden');
        }
        function closeVoucherModal() {
            document.getElementById('voucherModal').classList.add('hidden');
        }

        @if(session('error'))
            alert(" {{ session('error') }}");
        @endif
        @if(session('success'))
            // alert(" {{ session('success') }}");
        @endif

        // Loading state khi submit form đặt hàng
        document.querySelectorAll('form[data-loading]').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = ' Đang xử lý...';
                }
            });
        });

        // ========================================
        // CASCADING ADDRESS PICKER LOGIC
        // ========================================
        (function() {
            const API_BASE = 'https://provinces.open-api.vn/api';
            const provinceSelect = document.getElementById('addr_province');
            const districtSelect = document.getElementById('addr_district');
            const wardSelect     = document.getElementById('addr_ward');
            const detailInput    = document.getElementById('addr_detail');
            const hiddenInput    = document.getElementById('diachi_giaohang_hidden');
            const previewBox     = document.getElementById('address_preview');
            const previewText    = document.getElementById('address_preview_text');

            let selectedProvince = '';
            let selectedDistrict = '';
            let selectedWard = '';
            let addressModified = false;

            // Hiển thị địa chỉ đã lưu (nếu có)
            if (hiddenInput.value && hiddenInput.value.trim() !== '') {
                previewText.textContent = hiddenInput.value;
                previewBox.classList.remove('hidden');
            }

            // Loading indicator
            function setLoading(selectEl, loading) {
                if (loading) {
                    selectEl.classList.add('loading-select');
                    selectEl.disabled = true;
                } else {
                    selectEl.classList.remove('loading-select');
                    selectEl.disabled = false;
                }
            }

            // Reset a select dropdown
            function resetSelect(selectEl, placeholder) {
                selectEl.innerHTML = `<option value="" disabled selected>${placeholder}</option>`;
                selectEl.disabled = true;
            }

            // Compose full address and update hidden field + preview
            function composeAddress() {
                if (!addressModified) return;

                const detail = detailInput.value.trim();
                const ward = selectedWard;
                const district = selectedDistrict;
                const province = selectedProvince;

                const parts = [detail, ward, district, province].filter(p => p.length > 0);
                const fullAddress = parts.join(', ');

                hiddenInput.value = fullAddress;

                // Show/hide preview
                if (parts.length >= 3 || fullAddress.length > 0) {
                    previewText.textContent = fullAddress;
                    previewBox.classList.remove('hidden');
                    previewBox.style.animation = 'addressFadeIn 0.3s ease-out';
                } else {
                    previewBox.classList.add('hidden');
                }
            }

            // Fetch provinces on load
            async function loadProvinces() {
                setLoading(provinceSelect, true);
                try {
                    const res = await fetch(`${API_BASE}/p/`);
                    const data = await res.json();
                    data.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.code;
                        opt.textContent = p.name;
                        provinceSelect.appendChild(opt);
                    });
                } catch (err) {
                    console.error('Error loading provinces:', err);
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = '⚠ Lỗi tải dữ liệu';
                    provinceSelect.appendChild(opt);
                }
                provinceSelect.disabled = false;
                setLoading(provinceSelect, false);
            }

            // Province changed → load districts
            provinceSelect.addEventListener('change', async function() {
                addressModified = true;
                const code = this.value;
                selectedProvince = this.options[this.selectedIndex].text;
                selectedDistrict = '';
                selectedWard = '';

                resetSelect(districtSelect, '-- Đang tải Quận/Huyện... --');
                resetSelect(wardSelect, '-- Chọn Phường/Xã --');
                setLoading(districtSelect, true);

                try {
                    const res = await fetch(`${API_BASE}/p/${code}?depth=2`);
                    const data = await res.json();
                    resetSelect(districtSelect, '-- Chọn Quận/Huyện --');
                    data.districts.forEach(d => {
                        const opt = document.createElement('option');
                        opt.value = d.code;
                        opt.textContent = d.name;
                        districtSelect.appendChild(opt);
                    });
                    districtSelect.disabled = false;
                } catch (err) {
                    console.error('Error loading districts:', err);
                    resetSelect(districtSelect, '⚠ Lỗi tải dữ liệu');
                }
                setLoading(districtSelect, false);
                composeAddress();
            });

            // District changed → load wards
            districtSelect.addEventListener('change', async function() {
                addressModified = true;
                const code = this.value;
                selectedDistrict = this.options[this.selectedIndex].text;
                selectedWard = '';

                resetSelect(wardSelect, '-- Đang tải Phường/Xã... --');
                setLoading(wardSelect, true);

                try {
                    const res = await fetch(`${API_BASE}/d/${code}?depth=2`);
                    const data = await res.json();
                    resetSelect(wardSelect, '-- Chọn Phường/Xã --');
                    data.wards.forEach(w => {
                        const opt = document.createElement('option');
                        opt.value = w.code;
                        opt.textContent = w.name;
                        wardSelect.appendChild(opt);
                    });
                    wardSelect.disabled = false;
                } catch (err) {
                    console.error('Error loading wards:', err);
                    resetSelect(wardSelect, '⚠ Lỗi tải dữ liệu');
                }
                setLoading(wardSelect, false);
                composeAddress();
            });

            // Ward changed
            wardSelect.addEventListener('change', function() {
                addressModified = true;
                selectedWard = this.options[this.selectedIndex].text;
                composeAddress();
            });

            // Detail address changed
            detailInput.addEventListener('input', function() {
                addressModified = true;
                composeAddress();
            });

            // Form validation: ensure hidden input has value before submit
            const mainForm = document.querySelector('form[data-loading]');
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    composeAddress();
                    if (!hiddenInput.value || hiddenInput.value.trim().length < 5) {
                        e.preventDefault();
                        // Highlight the address section
                        provinceSelect.closest('.space-y-3').scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        if (!selectedProvince) {
                            provinceSelect.focus();
                            provinceSelect.classList.add('border-red-400', 'ring-2', 'ring-red-100');
                        } else if (!selectedDistrict) {
                            districtSelect.focus();
                            districtSelect.classList.add('border-red-400', 'ring-2', 'ring-red-100');
                        } else if (!selectedWard) {
                            wardSelect.focus();
                            wardSelect.classList.add('border-red-400', 'ring-2', 'ring-red-100');
                        } else {
                            detailInput.focus();
                            detailInput.classList.add('border-red-400', 'ring-2', 'ring-red-100');
                        }
                        
                        // Remove highlight after 3 seconds
                        setTimeout(() => {
                            [provinceSelect, districtSelect, wardSelect, detailInput].forEach(el => {
                                el.classList.remove('border-red-400', 'ring-2', 'ring-red-100');
                            });
                        }, 3000);
                        
                        return false;
                    }
                });
            }

            // Initialize
            loadProvinces();
        })();
    </script>
    <style>
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes addressFadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom select styling */
        .address-select {
            background-image: none !important;
            transition: all 0.2s ease;
        }
        .address-select:focus {
            box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.15);
        }
        .address-select option {
            padding: 8px 12px;
        }

        /* Loading state for selects */
        .loading-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cstyle%3E%40keyframes spin%7B100%25%7Btransform:rotate(360deg)%7D%7D%3C/style%3E%3Ccircle cx='12' cy='12' r='9' fill='none' stroke='%23FF6B35' stroke-width='2.5' stroke-dasharray='28' stroke-linecap='round'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 12 12' to='360 12 12' dur='0.8s' repeatCount='indefinite'/%3E%3C/circle%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 18px !important;
            padding-right: 40px !important;
        }

        /* Address preview animation */
        #address_preview {
            transition: all 0.3s ease;
        }
    </style>

    </form> <form id="form-go-voucher" action="{{ route('voucher.remove') }}" method="POST" class="hidden">
        @csrf
    </form>
</div>

@endsection