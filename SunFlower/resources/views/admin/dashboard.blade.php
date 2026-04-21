@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('page_title', 'Tổng quan hệ thống')

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card card-custom p-4 border-start border-4 border-warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Đơn hàng mới</h6>
                    <h3 class="fw-bold">{{ $donHangMoiCount }}</h3>
                </div>
                <i class="fa-solid fa-shopping-bag fa-2x text-warning"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card card-custom p-4 border-start border-4 border-success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted">Doanh thu ngày</h6>
                    <h3 class="fw-bold">{{ number_format($doanhThuNgay, 0, ',', '.') }}đ</h3>
                </div>
                <i class="fa-solid fa-money-bill-trend-up fa-2x text-success"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card card-custom p-4 border-start border-4 border-primary">
            <div>
                <h6 class="text-muted">Sản phẩm</h6>
                <h3 class="fw-bold">{{ $tongSanPham }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
    <div class="card card-custom p-4 border-start border-4 border-danger">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h6 class="text-muted">Tổng nhân viên</h6> <h3 class="fw-bold">{{ $tongNhanVien }}</h3> </div>
            <i class="fa-solid fa-users fa-2x text-danger"></i>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-custom p-4">
            <h5 class="fw-bold mb-4">Đơn hàng gần đây</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->madh }}</td>
                        <td>{{ $order->khachhang->hoten ?? 'Khách vãng lai' }}</td>
                        <td>{{ $order->ngaydat }}</td>
                        <td>
                            @if($order->trangthai == 'cho_xac_nhan')
                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            @elseif($order->trangthai == 'da_hoan_thanh')
                                <span class="badge bg-success">Hoàn thành</span>
                            @else
                                <span class="badge bg-secondary">{{ $order->trangthai }}</span>
                            @endif
                        </td>
                        <td>{{ number_format($order->tongtien, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection