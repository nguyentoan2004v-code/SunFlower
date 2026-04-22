@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')
@section('page_title', 'CHI TIẾT ĐƠN HÀNG: ' . $order->madon)

@section('content')
<div class="container-fluid mt-3 pb-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-truck-fast me-2"></i>Xử lý Đơn hàng</h6>
                </div>
                <div class="card-body">
                <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->ngaydat)->format('d/m/Y H:i') }}</p>
                
                {{-- Mượn tên từ bảng khachhang (Giả sử cột tên trong bảng khách hàng là 'tenkh' hoặc 'hoten') --}}
                <p><strong>Người nhận:</strong> {{  $order->khachhang->hoten ?? 'Khách vãng lai' }}</p>
                
                <p><strong>Điện thoại:</strong> {{ $order->sdt_nhan }}</p>
                
                {{-- Đổi thành diachi_giao cho khớp với Database --}}
                <p><strong>Địa chỉ:</strong> {{ $order->diachi_giao }}</p>
                
                <p><strong>Ghi chú:</strong> {{ $order->ghichu ?? 'Không có' }}</p>
                <hr>
                    
                    <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                            <select name="trangthai" class="form-select border-primary" {{ $order->trangthai == 'Đã hoàn thành' ? 'disabled' : '' }}>
                                <option value="Chờ xác nhận" {{ $order->trangthai == 'Chờ xác nhận' ? 'selected' : '' }}>Chờ xác nhận</option>
                                <option value="Đang giao" {{ $order->trangthai == 'Đang giao' ? 'selected' : '' }}>Đang giao</option>
                                <option value="Đã hoàn thành" {{ $order->trangthai == 'Đã hoàn thành' ? 'selected' : '' }}>Đã hoàn thành</option>
                                <option value="Đã hủy" {{ $order->trangthai == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                            </select>
                        </div>
                        
                        @if($order->trangthai != 'Đã hoàn thành' && $order->trangthai != 'Đã hủy')
                            <button type="submit" class="btn text-white w-100" style="background-color: var(--sunflower-orange);">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu Trạng Thái
                            </button>
                        @endif
                    </form>

                    @if($hoadon)
                        <div class="alert alert-success mt-4 mb-0">
                            <i class="fa-solid fa-receipt me-2"></i> Đã xuất hóa đơn: <strong>{{ $hoadon->mahd }}</strong>
                            <br><small class="text-muted">Lập ngày: {{ \Carbon\Carbon::parse($hoadon->ngaylap)->format('d/m/Y') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);"><i class="fa-solid fa-box-open me-2"></i>Chi tiết sản phẩm</h6>
                    <h5 class="m-0 text-danger fw-bold">Tổng: {{ number_format($order->tongtien, 0, ',', '.') }} ₫</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Sản phẩm</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end pe-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->sanphams as $sp)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ route('product.image', $sp->masp) }}" class="rounded shadow-sm me-3" style="width:50px; height:50px; object-fit:cover;">
                                            <span class="fw-medium">{{ $sp->sanpham->tensp ?? $sp->masp }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ number_format($sp->giaban, 0, ',', '.') }} ₫</td>
                                    <td class="text-center fw-bold">{{ $sp->pivot->soluong }}</td>
                                    <td class="text-end pe-4 fw-bold text-danger">{{ number_format($sp->giaban * $sp->pivot->soluong, 0, ',', '.') }} ₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection