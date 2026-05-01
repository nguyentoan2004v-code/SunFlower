<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In Hóa Đơn - {{ $hoadon->mahd }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #333;
            background-color: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }
        .header-logo {
            font-size: 28px;
            font-weight: bold;
            color: #f39c12; /* Màu cam SunFlower */
            text-transform: uppercase;
        }
        .invoice-title {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            display: inline-block;
        }
        .table-invoice thead {
            background-color: #f8f9fa;
        }
        .footer-signature {
            margin-top: 50px;
        }
        /* Cấu hình khi in */
        @media print {
            .no-print {
                display: none !important;
            }
            .invoice-box {
                border: none;
                padding: 0;
            }
            body {
                margin: 0;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box shadow-sm mt-4 mb-4">
        <div class="row border-bottom pb-3">
            <div class="col-6">
                <div class="header-logo">SunFlower Shop</div>
                <p class="mb-0">Chuyên cung cấp hoa tươi & quà tặng</p>
                <p class="mb-0">Địa chỉ: 180 Cao Lỗ, Phường 4, Quận 8, TP.HCM</p>
                <p class="mb-0">Điện thoại: 0123 456 789</p>
            </div>
            <div class="col-6 text-end">
                <h4 class="text-primary mb-1">HÓA ĐƠN</h4>
                <p class="mb-0">Số: <strong>{{ $hoadon->mahd }}</strong></p>
                <p class="mb-0">Ngày xuất: {{ \Carbon\Carbon::parse($hoadon->ngayxuat)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="row mt-4 mb-4">
            <div class="col-7">
                <h6 class="text-uppercase fw-bold border-bottom pb-1">Thông tin khách hàng</h6>
                <p class="mb-1"><span class="info-label">Khách hàng:</span> {{ $hoadon->donhang->khachhang->hoten ?? 'Khách vãng lai' }}</p>
                <p class="mb-1"><span class="info-label">Số điện thoại:</span> {{ $hoadon->donhang->sdt_nhan }}</p>
                <p class="mb-1"><span class="info-label">Địa chỉ giao:</span> {{ $hoadon->donhang->diachi_giao }}</p>
            </div>
            <div class="col-5">
                <h6 class="text-uppercase fw-bold border-bottom pb-1">Chi tiết thanh toán</h6>
                <p class="mb-1"><span class="info-label">Mã đơn hàng:</span> {{ $hoadon->madon }}</p>
                <p class="mb-1"><span class="info-label">Phương thức:</span> {{ $hoadon->ptthanhtoan }}</p>
                <p class="mb-1"><span class="info-label">Trạng thái:</span> Đã thanh toán</p>
            </div>
        </div>

        <table class="table table-bordered table-invoice">
            <thead>
                <tr class="text-center">
                    <th width="5%">STT</th>
                    <th>Tên sản phẩm</th>
                    <th width="15%">Số lượng</th>
                    <th width="20%">Đơn giá</th>
                    <th width="20%">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hoadon->chitiets as $index => $ct)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    {{-- Sử dụng tensp snapshot từ chitiet_hoadon --}}
                    <td>{{ $ct->tensp }}</td>
                    <td class="text-center">{{ $ct->soluong }}</td>
                    <td class="text-end">{{ number_format($ct->dongia, 0, ',', '.') }} ₫</td>
                    <td class="text-end">{{ number_format($ct->soluong * $ct->dongia, 0, ',', '.') }} ₫</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Tổng tiền hàng:</td>
                    <td class="text-end">{{ number_format($hoadon->tongtien - $hoadon->thue, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end fw-bold">Thuế (VAT):</td>
                    <td class="text-end">{{ number_format($hoadon->thue, 0, ',', '.') }} ₫</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end fw-bold text-uppercase text-danger">Tổng cộng thanh toán:</td>
                    <td class="text-end fw-bold text-danger">{{ number_format($hoadon->tongtien, 0, ',', '.') }} ₫</td>
                </tr>
            </tfoot>
        </table>

        <div class="row footer-signature text-center">
            <div class="col-4">
                <p class="fw-bold">Người mua hàng</p>
                <small class="text-muted">(Ký, ghi rõ họ tên)</small>
            </div>
            <div class="col-4">
                </div>
            <div class="col-4">
                <p class="fw-bold">Người lập hóa đơn</p>
                <small class="text-muted">(Ký, ghi rõ họ tên)</small>
                <div style="margin-top: 60px;" class="fw-bold">{{ auth()->user()->name ?? 'Nhân viên bán hàng' }}</div>
            </div>
        </div>

        <div class="text-center mt-5 no-print border-top pt-3">
            <button onclick="window.print()" class="btn btn-primary px-4 me-2">
                <i class="fa-solid fa-print"></i> Thực hiện in
            </button>
            <button onclick="window.close()" class="btn btn-secondary px-4">Đóng cửa sổ</button>
        </div>
    </div>
</body>
</html>