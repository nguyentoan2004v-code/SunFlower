@extends('layouts.admin')

@section('title', 'Lịch sử mua hàng')
@section('page_title', 'LỊCH SỬ MUA HÀNG')

@section('content')
<style>
    /* BỔ SUNG DARK MODE */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .table { color: #e9ecef !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.05) !important; }
    [data-bs-theme="dark"] .pagination .page-link { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .pagination .page-item.active .page-link { background-color: var(--sunflower-orange, #FF8C00) !important; border-color: var(--sunflower-orange, #FF8C00) !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .pagination .page-link:hover { background-color: #373b3e !important; color: #ffffff !important; }
    
    [data-bs-theme="dark"] .bg-light { background-color: #1a1d20 !important; }
    [data-bs-theme="dark"] .text-dark { color: #e9ecef !important; }
</style>

<div class="container-fluid mt-4">
    <!-- Block thông tin khách hàng phía trên -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold text-dark"><i class="fa-solid fa-user me-2" style="color: var(--sunflower-orange);"></i> {{ $khachhang->hoten }}</h5>
                <p class="mb-0 text-muted">
                    <i class="fa-solid fa-phone me-1"></i> {{ $khachhang->sdt }} | 
                    <i class="fa-solid fa-map-marker-alt me-1"></i> {{ $khachhang->diachi }}
                </p>
            </div>
            <a href="{{ route('admin.khachhang.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
        </div>
    </div>

    <!-- Bảng danh sách đơn hàng -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> Danh sách đơn hàng đã mua
            </h6>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mã Đơn</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Địa chỉ nhận hàng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donhangs as $dh)
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">{{ $dh->madon }}</td>
                                <td>{{ \Carbon\Carbon::parse($dh->ngaydat)->format('d/m/Y H:i') }}</td>
                                <td class="fw-bold text-danger">{{ number_format($dh->tongtien, 0, ',', '.') }} ₫</td>
                                <td>
                                    @if($dh->trangthai == 'Đã hoàn thành' || $dh->trangthai == 'Đã giao')
                                        <span class="badge bg-success rounded-pill px-3">{{ $dh->trangthai }}</span>
                                    @elseif($dh->trangthai == 'Đã hủy')
                                        <span class="badge bg-danger rounded-pill px-3">{{ $dh->trangthai }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark rounded-pill px-3">{{ $dh->trangthai }}</span>
                                    @endif
                                </td>
                                <td>{{ $dh->diachi_giao }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Khách hàng này chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($donhangs->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $donhangs->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection