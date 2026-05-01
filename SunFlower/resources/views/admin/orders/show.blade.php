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
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                        <div class="mt-1">
                            @if($order->trangthai == 'Chờ xác nhận')
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">Chờ xác nhận</span>
                            @elseif($order->trangthai == 'Đang giao')
                                <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill">Đang giao</span>
                            @elseif($order->trangthai == 'Đã hoàn thành')
                                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">Đã hoàn thành</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill">{{ $order->trangthai }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Xử lý nút bấm tuần tự --}}
                    @if($order->trangthai != 'Đã hoàn thành' && $order->trangthai != 'Đã hủy')
                        <hr>
                        <label class="form-label fw-bold">Thao tác:</label>
                        <div class="d-grid gap-2">
                            
                            @if($order->trangthai == 'Chờ xác nhận')
                                <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="trangthai" value="Đang giao">
                                    <button type="submit" class="btn text-dark w-100 fw-bold" style="background-color: #0dcaf0;">
                                        <i class="fa-solid fa-truck-arrow-right me-2"></i> Xác nhận & Chuyển giao hàng
                                    </button>
                                </form>
                            @endif

                            @if($order->trangthai == 'Đang giao')
                                <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="trangthai" value="Đã hoàn thành">
                                    <button type="submit" class="btn btn-success w-100 fw-bold">
                                        <i class="fa-solid fa-check-double me-2"></i> Xác nhận Đã hoàn thành
                                    </button>
                                </form>
                            @endif

                            {{-- Nút Hủy đơn (luôn hiển thị nếu chưa hoàn thành hoặc chưa hủy) --}}
                            <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="trangthai" value="Đã hủy">
                                <button type="submit" class="btn btn-outline-secondary w-100">
                                    <i class="fa-solid fa-ban me-2"></i> Hủy đơn hàng
                                </button>
                            </form>
                        </div>
                    @endif

                    @if(!$hoadon)
                        {{-- Chỉ cho phép xuất hóa đơn khi đơn hàng đã hoàn thành hoặc đang giao --}}
                        @if(in_array($order->trangthai, ['Đã hoàn thành']))
                            <div class="mt-4">
                                <form action="{{ route('admin.orders.export-invoice', $order->madon) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                                        <i class="fa-solid fa-file-invoice me-2"></i> Xuất Hóa Đơn
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-success mt-4 mb-0 border-0 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa-solid fa-receipt me-2"></i> <strong>{{ $hoadon->mahd }}</strong>
                                    <br><small class="text-muted">Lập ngày: {{ \Carbon\Carbon::parse($hoadon->ngaylap)->format('d/m/Y') }}</small>
                                </div>
                                <a href="{{ route('admin.orders.print-invoice', $hoadon->mahd) }}" target="_blank" class="btn btn-light btn-sm border">
                                    <i class="fa-solid fa-print"></i> In
                                </a>
                            </div>
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