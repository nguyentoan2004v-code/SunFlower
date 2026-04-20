@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    @if(session('error'))
    <div style="background-color: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
        LỖI DATABASE: {{ session('error') }}
    </div>
@endif
    <form action="{{ route('checkout') }}" method="POST">
        @csrf
        <div class="flex flex-col md:flex-row gap-8">
            <div class="w-full md:w-2/3">
                <h1 class="text-3xl font-extrabold mb-8 text-gray-900">Giỏ hàng của bạn</h1>

                @if(session('cart') && count(session('cart')) > 0)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-6 py-4">Chọn</th>
                                    <th class="px-6 py-4">Sản phẩm</th>
                                    <th class="px-6 py-4 text-center">Số lượng</th>
                                    <th class="px-6 py-4 text-right">Thành tiền</th>
                                    <th class="px-6 py-4 text-center">Thao tác</th> </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach(session('cart') as $id => $details)
                                    <tr class="cart-item-row" data-id="{{ $id }}" data-price="{{ $details['price'] }}" data-old-price="{{ $details['old_price'] ?? $details['price'] }}">
                                        <td class="px-6 py-6">
                                            <input type="checkbox" name="selected_items[]" value="{{ $id }}" checked
                                                   class="item-checkbox w-5 h-5 rounded border-gray-300 text-[#FF6B35] focus:ring-[#FF6B35] cursor-pointer">
                                        </td>
                                        
                                        <td class="px-6 py-6 flex items-center gap-4">
                                            <img src="{{ asset('storage/image/' . $details['image']) }}" class="w-16 h-16 rounded-xl object-cover border border-gray-100">
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $details['name'] }}</p>
                                                <p class="text-[#FF6B35] font-medium">{{ number_format($details['price'], 0, ',', '.') }} ₫</p>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-6">
                                            <div class="flex items-center justify-center border border-gray-200 rounded-xl w-32 mx-auto overflow-hidden bg-white">
                                                <button type="button" class="px-3 py-2 hover:bg-gray-100 btn-minus transition text-gray-600 font-bold">-</button>
                                                <input type="text" class="w-12 text-center border-none focus:ring-0 qty-input font-bold text-gray-700" 
                                                       value="{{ $details['quantity'] }}" readonly>
                                                <button type="button" class="px-3 py-2 hover:bg-gray-100 btn-plus transition text-gray-600 font-bold">+</button>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-6 text-right font-bold text-gray-900 row-total">
                                            {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }} ₫
                                        </td>
                                        
                                        <td class="px-6 py-6 text-center">
                                            <a href="{{ route('cart.remove', $id) }}" 
                                               class="text-gray-400 hover:text-red-500 transition-colors inline-block p-2"
                                               onclick="return confirm('Bạn có chắc muốn xóa đóa hoa này khỏi giỏ hàng?')">
                                                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-gray-200 shadow-sm">
                        <div class="w-20 h-20 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-[#FF6B35]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Giỏ hàng đang trống</h2>
                        <p class="text-gray-500 mb-6">Có vẻ như bạn chưa chọn được đóa hoa nào ưng ý.</p>
                        <a href="{{ route('home') }}" class="inline-block bg-[#FF6B35] hover:bg-orange-600 text-white px-8 py-3.5 rounded-xl font-bold shadow-lg shadow-orange-100 transition">
                            Khám phá cửa hàng
                        </a>
                    </div>
                @endif
            </div>

            @if(session('cart') && count(session('cart')) > 0)
            <div class="w-full md:w-1/3">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sticky top-24">
                    <h2 class="text-xl font-extrabold text-gray-900 mb-6">Tóm tắt đơn hàng</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between text-gray-600 font-medium">
                            <span>Tạm tính</span>
                            <span id="temp-total">0 ₫</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-600 font-medium">
                            <span>Phí vận chuyển</span>
                            <span class="text-green-600">Miễn phí</span>
                        </div>

                        <div class="flex justify-between text-gray-600 font-medium">
                            <span>Giảm giá</span>
                            <span class="text-red-500" id="discount-total">-0 ₫</span>
                        </div>

                        <hr class="border-gray-100 my-4">

                        <div class="flex justify-between items-start pt-2">
                            <div>
                                <span class="text-lg font-bold text-gray-900 block">Tổng tiền</span>
                                <span class="text-[11px] text-gray-400 font-medium italic">(Đã bao gồm VAT nếu có)</span>
                            </div>
                            <span class="text-2xl font-extrabold text-[#FF6B35]" id="final-total">0 ₫</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-[#FF6B35] hover:bg-orange-600 text-white py-4 rounded-2xl font-bold text-lg shadow-lg shadow-orange-100 transition active:scale-[0.98]">
                        Thanh toán ngay
                    </button>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<script>
    // Bắt sự kiện bấm nút + / -
    document.querySelectorAll('.btn-plus, .btn-minus').forEach(button => {
        button.addEventListener('click', function() {
            const row = this.closest('.cart-item-row');
            const input = row.querySelector('.qty-input');
            const id = row.dataset.id;
            let qty = parseInt(input.value);

            if (this.classList.contains('btn-plus')) qty++;
            else if (qty > 1) qty--;

            input.value = qty;
            
            // Gửi cập nhật lên Server bằng Ajax
            fetch('{{ route("cart.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id, quantity: qty })
            }).then(() => updateTotals());
        });
    });

    // Tính lại tiền khi bấm vào Checkbox
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateTotals);
    });

    // Hàm tính tổng tiền chính
    // Hàm tính tổng tiền chính
    function updateTotals() {
        let totalToPay = 0;   // Tổng tiền khách phải trả (đã giảm)
        let totalOriginal = 0; // Tổng giá trị gốc của hoa
        let totalDiscount = 0; // Tổng tiền được giảm
        
        document.querySelectorAll('.cart-item-row').forEach(row => {
            const qty = parseInt(row.querySelector('.qty-input').value);
            const price = parseFloat(row.dataset.price);
            
            // Lấy giá gốc, nếu không có thì mặc định bằng giá hiện tại
            const oldPrice = parseFloat(row.dataset.oldPrice) || price;
            
            const subtotal = qty * price;
            
            // Cập nhật giá tiền ở cột "Thành tiền" của từng dòng
            row.querySelector('.row-total').innerText = subtotal.toLocaleString('vi-VN') + ' ₫';
            
            // Nếu người dùng có tích chọn vào ô Checkbox thì mới tính vào hóa đơn
            if (row.querySelector('.item-checkbox').checked) {
                totalToPay += subtotal;
                totalOriginal += (qty * oldPrice);
            }
        });
        
        // Tính ra số tiền được giảm giá
        totalDiscount = totalOriginal - totalToPay;
        
        // Cập nhật lên giao diện
        const tempTotalEl = document.getElementById('temp-total');
        if(tempTotalEl) tempTotalEl.innerText = totalOriginal.toLocaleString('vi-VN') + ' ₫';
        
        const discountTotalEl = document.getElementById('discount-total');
        if(discountTotalEl) discountTotalEl.innerText = '-' + totalDiscount.toLocaleString('vi-VN') + ' ₫';
        
        const finalTotalEl = document.getElementById('final-total');
        if(finalTotalEl) finalTotalEl.innerText = totalToPay.toLocaleString('vi-VN') + ' ₫';
    }
    
    // Gọi hàm một lần khi trang vừa load xong để Checkbox được tính tiền ngay
    updateTotals();
</script>
@endsection