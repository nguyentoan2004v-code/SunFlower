@extends('layouts.admin')

@section('title', 'Truy vết Lô Hàng')
@section('page_title', 'TRUY VẾT LÔ HÀNG')

@section('content')
<style>
    .timeline {
        position: relative;
        padding-left: 3rem;
        margin-top: 2rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e2e8f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -2.35rem;
        top: 0.3rem;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: var(--sunflower-orange, #f59e0b);
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2);
    }
    .timeline-item.import::before { background-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2); }
    .timeline-item.waste::before { background-color: #ef4444; box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2); }
    .timeline-item.order::before { background-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2); }
    
    .timeline-date {
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    .timeline-content {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .timeline-content p { margin-bottom: 0; }
</style>

<div class="container-fluid mt-3">
    <div class="mb-4">
        <a href="{{ route('admin.longuyenlieu.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <div class="row">
        {{-- Thông tin tổng quan Lô --}}
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fa-solid fa-box-archive me-2"></i> Hồ sơ Lô hàng</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item py-3">
                        <small class="text-muted d-block">Mã Lô</small>
                        <strong class="font-monospace fs-5 text-primary">{{ $lot->malo }}</strong>
                    </li>
                    <li class="list-group-item py-3">
                        <small class="text-muted d-block">Nguyên liệu</small>
                        <strong>{{ $lot->nguyenLieu->ten_nl ?? 'N/A' }}</strong>
                    </li>
                    <li class="list-group-item py-3">
                        <small class="text-muted d-block">Số lượng nhập ban đầu</small>
                        <strong class="text-dark fs-5">{{ number_format($lot->soluong_bandau) }}</strong>
                    </li>
                    <li class="list-group-item py-3">
                        <small class="text-muted d-block">Tồn kho còn lại</small>
                        <strong class="{{ $lot->soluong_hientai > 0 ? 'text-success' : 'text-danger' }} fs-5">
                            {{ number_format($lot->soluong_hientai) }}
                        </strong>
                    </li>
                    <li class="list-group-item py-3">
                        <small class="text-muted d-block">Hạn sử dụng</small>
                        <strong>{{ $lot->hsd ? \Carbon\Carbon::parse($lot->hsd)->format('d/m/Y') : 'Không giới hạn' }}</strong>
                    </li>
                    <li class="list-group-item py-3">
                        <small class="text-muted d-block">Phiếu nhập nguồn</small>
                        @if($lot->phieuNhap)
                            <a href="{{ route('admin.phieunhapkho.show', $lot->id_phieu_nhap) }}" class="text-decoration-none fw-bold">
                                {{ $lot->phieuNhap->code }}
                            </a>
                            <br>
                            <small class="text-muted">Người lập: {{ $lot->phieuNhap->nhanVien->hoten ?? 'N/A' }}</small>
                        @else
                            <strong>Không xác định (Dữ liệu đồng bộ)</strong>
                        @endif
                    </li>
                </ul>
            </div>
        </div>

        {{-- Dòng thời gian --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4">Lịch sử Biến động ("Cuộc đời" của Lô)</h4>
                    
                    @if($logs->isEmpty())
                        <div class="alert alert-info border-0 shadow-sm py-4 text-center">
                            <i class="fa-solid fa-folder-open fs-1 text-info mb-3 d-block"></i>
                            <strong>Chưa có phát sinh xuất kho nào.</strong><br>
                            Lô hàng này vẫn còn nguyên vẹn trong kho.
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($logs as $log)
                                @php
                                    $timelineClass = 'order';
                                    $icon = 'fa-check-double text-success';
                                    if ($log->loai_gd == 'import') {
                                        $timelineClass = 'import';
                                        $icon = 'fa-arrow-down-to-line text-primary';
                                    } elseif ($log->loai_gd == 'waste') {
                                        $timelineClass = 'waste';
                                        $icon = 'fa-trash-can text-danger';
                                    }
                                @endphp

                                <div class="timeline-item {{ $timelineClass }}">
                                    <div class="timeline-date">
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                    </div>
                                    <div class="timeline-content d-flex gap-3 align-items-center">
                                        <div class="fs-4"><i class="fa-solid {{ $icon }}"></i></div>
                                        <div class="flex-grow-1">
                                            <p class="fw-bold text-dark mb-1">
                                                @if($log->loai_gd == 'import')
                                                    Nhập kho
                                                @elseif($log->loai_gd == 'waste')
                                                    Xuất hủy nguyên liệu
                                                @elseif($log->loai_gd == 'order_complete')
                                                    Xuất cắm hoa (Hoàn thành đơn)
                                                @else
                                                    Giao dịch khác
                                                @endif
                                            </p>
                                            <p class="text-muted small mb-0">{{ $log->ghichu }}</p>
                                        </div>
                                        <div class="text-end ms-3">
                                            <h5 class="fw-bold mb-1 {{ $log->soluong > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $log->soluong > 0 ? '+' : '' }}{{ number_format($log->soluong) }}
                                            </h5>
                                            <small class="text-muted d-block"><i class="fa-solid fa-user me-1"></i> {{ $log->nhanvien->hoten ?? 'HT' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            {{-- Gốc sinh ra lô --}}
                            @if($lot->phieuNhap)
                            <div class="timeline-item import">
                                <div class="timeline-date">
                                    {{ $lot->created_at->format('d/m/Y H:i:s') }}
                                </div>
                                <div class="timeline-content d-flex gap-3 align-items-center bg-light">
                                    <div class="fs-4"><i class="fa-solid fa-box-open text-primary"></i></div>
                                    <div class="flex-grow-1">
                                        <p class="fw-bold text-dark mb-1">Hệ thống tạo Lô</p>
                                        <p class="text-muted small mb-0">Lô được sinh ra từ Phiếu nhập {{ $lot->phieuNhap->code }}</p>
                                    </div>
                                    <div class="text-end ms-3">
                                        <h5 class="fw-bold mb-1 text-success">+{{ number_format($lot->soluong_bandau) }}</h5>
                                        <small class="text-muted d-block"><i class="fa-solid fa-user me-1"></i> {{ $lot->phieuNhap->nhanVien->hoten ?? 'HT' }}</small>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
