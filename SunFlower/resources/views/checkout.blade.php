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

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ giao hàng</label>
                            <textarea name="diachi_giaohang" rows="3" required
                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FF6B35] focus:border-transparent outline-none @error('diachi_giaohang') border-red-400 @enderror"
                                      placeholder="Số nhà, tên đường, phường/xã, quận/huyện...">{{ old('diachi_giaohang', Auth::guard('khachhang')->user()->diachi ?? '') }}</textarea>
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
            alert("⚠️ {{ session('error') }}");
        @endif
        @if(session('success'))
            // alert("✅ {{ session('success') }}"); // (Tuỳ chọn: Bạn có thể bật dòng này nếu muốn thông báo khi áp mã thành công)
        @endif

        // Loading state khi submit form đặt hàng
        document.querySelectorAll('form[data-loading]').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '⏳ Đang xử lý...';
                }
            });
        });
    </script>
    <style>
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    </form> <form id="form-go-voucher" action="{{ route('voucher.remove') }}" method="POST" class="hidden">
        @csrf
    </form>
</div>

@endsection