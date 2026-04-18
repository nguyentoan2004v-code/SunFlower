<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán SunFlower</title>
    @vite(['resources/js/app.js'])
</head>
<body>
    <div style="max-width: 500px; margin: 0 auto; padding: 20px;">
        <h2>🛒 Thanh toán đơn hàng</h2>
        
        <form id="checkout-form">
            <div style="margin-bottom: 10px;">
                <label>Họ tên người nhận:</label><br>
                <input type="text" id="hoten" required style="width: 100%;">
            </div>

            <div style="margin-bottom: 10px;">
                <label>Số điện thoại:</label><br>
                <input type="text" id="sdt" required style="width: 100%;">
            </div>

            <div style="margin-bottom: 10px;">
                <label>Địa chỉ giao hàng:</label><br>
                <textarea id="diachi" required style="width: 100%;"></textarea>
            </div>

            <div style="margin-bottom: 10px;">
                <label>Ghi chú:</label><br>
                <textarea id="ghichu" style="width: 100%;"></textarea>
            </div>
            
           <h3>Tổng tiền: <span id="tongtien-hienthi">0</span> VNĐ</h3>
            
            <button type="submit" id="btn-submit" style="padding: 10px 20px; cursor: pointer;">
                Xác nhận thanh toán
            </button>
        </form>
    </div>
</body>
</html>