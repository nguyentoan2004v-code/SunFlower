import './bootstrap';

document.addEventListener('DOMContentLoaded', async () => {
    // 1. Khởi tạo giỏ hàng (giữ nguyên logic cũ)
    if (!localStorage.getItem('cart')) {
        localStorage.setItem('cart', JSON.stringify([
            { masp: "SP00000001", soluong: 2, dongia: 500000 },
            { masp: "SP00000002", soluong: 1, dongia: 250000 }
        ]));
    }

    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const calculateTotal = () => cart.reduce((sum, item) => sum + (item.soluong * item.dongia), 0);
    const tongtien = calculateTotal();
    
    const tongTienEl = document.getElementById('tongtien-hienthi');
    if (tongTienEl) tongTienEl.innerText = tongtien.toLocaleString();

    // 2. Xử lý khi bấm nút Thanh toán
    const form = document.getElementById('checkout-form');
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            console.log("--- BẮT ĐẦU CHECKOUT ---");

            // KIỂM TRA THỬ CÁC PHẦN TỬ HTML CÓ TỒN TẠI KHÔNG
            const elHoTen = document.getElementById('hoten');
            const elSdt = document.getElementById('sdt');
            const elDiaChi = document.getElementById('diachi');
            const elGhiChu = document.getElementById('ghichu');

            if (!elHoTen || !elSdt || !elDiaChi) {
                alert("Lỗi: Không tìm thấy các ô nhập liệu trong HTML. Kiểm tra lại ID!");
                return;
            }

            const payload = {
                hoten_nguoi_nhan: elHoTen.value,
                sdt_nhan: elSdt.value,
                diachi_giao: elDiaChi.value,
                ghichu: elGhiChu ? elGhiChu.value : '',
                tongtien: tongtien,
                cart: cart.map(item => ({
                    masp: item.masp,
                    soluong: item.soluong,
                    dongia: item.dongia
                }))
            };

            console.log("Dữ liệu chuẩn bị gửi:", payload);

            const btn = document.getElementById('btn-submit');
            btn.innerText = "Đang gửi...";
            btn.disabled = true;

            try {
                // KIỂM TRA AXIOS TRƯỚC KHI GỌI
                if (!window.axios) {
                    throw new Error("Axios chưa được nạp! Kiểm tra bootstrap.js");
                }

                const res = await window.axios.post('/customer/checkout', payload);
                console.log("Kết quả từ Server:", res.data);
                
                if (res.data.status === 'success') {
                   alert('🎉 ' + res.data.message);
                    localStorage.removeItem('cart');
                    window.location.reload();
                }
            } catch (error) {
                // LOG TOÀN BỘ ĐỐI TƯỢNG LỖI RA ĐỂ SOI
                console.error("CHI TIẾT LỖI XẢY RA:", error);

                if (error.response) {
                    alert('Lỗi Server (404/500): ' + (error.response.data.message || 'Lỗi không xác định'));
                } else {
                    alert('Lỗi Client (Chưa gửi được tới server): ' + error.message);
                }
            } finally {
                btn.innerText = "Xác nhận thanh toán";
                btn.disabled = false;
            }
        });
    }
});