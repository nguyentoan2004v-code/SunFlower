@extends('layouts.admin')

@section('title', 'Quản lý Đơn hàng')
@section('page_title', 'DANH SÁCH ĐƠN HÀNG')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-cart-shopping me-2"></i> Danh sách Đơn đặt hàng
            </h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã ĐH</th>
                            <th>Ngày đặt</th>
                            <th>Khách hàng</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $order->madon }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->ngaydat)->format('d/m/Y H:i') }}</td>
                            <td>{{ $order->hoten_nhan ?? 'Khách lẻ' }}</td>
                            <td class="text-danger fw-bold">{{ number_format($order->tongtien, 0, ',', '.') }} ₫</td>
                            <td>
                                @if($order->trangthai == 'Chờ xác nhận')
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Chờ xác nhận</span>
                                @elseif($order->trangthai == 'Đang giao')
                                    <span class="badge bg-info text-dark px-3 py-2 rounded-pill">Đang giao</span>
                                @elseif($order->trangthai == 'Đã hoàn thành')
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Đã hoàn thành</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 rounded-pill">{{ $order->trangthai }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->madon) }}" class="btn btn-sm text-white shadow-sm" style="background-color: var(--sunflower-orange);">
                                    <i class="fa-solid fa-eye"></i> Xem & Xử lý
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có đơn hàng nào!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($orders->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection