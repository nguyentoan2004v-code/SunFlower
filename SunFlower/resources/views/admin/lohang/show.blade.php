@extends('layouts.admin')

@section('title', 'Chi tiết Lô Hàng ' . $loHang->malo)
@section('page_title', 'CHI TIẾT LÔ HÀNG: ' . $loHang->malo)

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE CHO CHI TIẾT LÔ HÀNG
       ========================================== */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .text-dark, [data-bs-theme="dark"] p strong { color: #e9ecef !important; }
    [data-bs-theme="dark"] p { color: #dee2e6 !important; }
    [data-bs-theme="dark"] hr { border-color: #495057 !important; }
    [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; border-color: #495057 !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .btn-light { background-color: #343a40 !important; color: #dee2e6 !important; border-color: #495057 !important; }
    [data-bs-theme="dark"] .btn-light:hover { background-color: #495057 !important; color: #ffffff !important; }

    /* Timeline */
    [data-bs-theme="dark"] .timeline-content { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .timeline-content.type-destroy { background-color: rgba(220,53,69,0.08) !important; }

    /* ==========================================
       CUSTOM STYLES
       ========================================== */
    .info-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.3px; color: #adb5bd; margin-bottom: 2px; }
    .info-value { font-size: 0.95rem; font-weight: 600; color: #2d3748; }
    [data-bs-theme="dark"] .info-label { color: #6c757d; }
    [data-bs-theme="dark"] .info-value { color: #e9ecef; }

    .stock-progress-lg { height: 12px; border-radius: 6px; background: #e9ecef; overflow: hidden; }
    .stock-progress-lg .bar { height: 100%; border-radius: 6px; }
    .stock-progress-lg .bar.high { background: #198754; }
    .stock-progress-lg .bar.medium { background: #ffc107; }
    .stock-progress-lg .bar.low { background: #dc3545; }
    .stock-progress-lg .bar.empty { background: #dee2e6; }
    [data-bs-theme="dark"] .stock-progress-lg { background: #373b3e; }

    .timeline { position: relative; padding-left: 26px; }
    .timeline::before { content: ''; position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
    [data-bs-theme="dark"] .timeline::before { background: #373b3e; }
    .timeline-item { position: relative; margin-bottom: 20px; padding-left: 16px; }
    .timeline-item::before {
        content: ''; position: absolute; left: -19px; top: 5px;
        width: 12px; height: 12px; border-radius: 50%; border: 2px solid white;
    }
    .timeline-item.type-import::before { background: var(--sunflower-orange); box-shadow: 0 0 0 2px var(--sunflower-orange); }
    .timeline-item.type-destroy::before { background: #dc3545; box-shadow: 0 0 0 2px #dc3545; }
    .timeline-date { font-size: 0.78rem; font-weight: 700; color: #adb5bd; }
    .timeline-content {
        background: #f8f9fa; border-radius: 8px; padding: 12px 16px;
        margin-top: 4px; font-size: 0.88rem; border: 1px solid #e9ecef;
    }
    .timeline-content.type-destroy { background: #fff5f5; border-left: 3px solid #dc3545; }
</style>

<div class="container-fluid mt-3 pb-5">
    <div class="mb-3">
        <a href="{{ route('admin.lohang.index') }}" class="text-decoration-none text-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        {{-- ====== CỘT TRÁI ====== --}}
        <div class="col-lg-8 mb-4">
            {{-- Thông tin lô hàng --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                        <i class="fa-solid fa-boxes-stacked me-2"></i> Chi tiết Lô Hàng — {{ $loHang->malo }}
                    </h5>
                    @php
                        $today = \Carbon\Carbon::today();
                        $hetHan = \Carbon\Carbon::parse($loHang->ngayhethan)->lt($today);
                        $hetHang = $loHang->soluong_ton <= 0;
                        $tonThap = !$hetHang && $loHang->soluong_nhap > 0 && ($loHang->soluong_ton / $loHang->soluong_nhap) <= 0.2;
                    @endphp
                    @if($hetHan)
                        <span class="badge bg-secondary px-3 py-2 rounded-pill">Hết hạn</span>
                    @elseif($hetHang)
                        <span class="badge bg-danger px-3 py-2 rounded-pill">Hết hàng</span>
                    @elseif($tonThap)
                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">Tồn thấp</span>
                    @else
                        <span class="badge bg-success px-3 py-2 rounded-pill">Còn hàng</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="info-label">Số lượng nhập</div>
                            <div class="info-value">{{ number_format($loHang->soluong_nhap) }} bông</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Số lượng tồn</div>
                            <div class="info-value {{ $hetHang ? 'text-danger' : ($tonThap ? 'text-warning' : 'text-success') }}">
                                {{ number_format($loHang->soluong_ton) }} bông
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Giá nhập</div>
                            <div class="info-value">{{ $loHang->gia_nhap ? number_format($loHang->gia_nhap) . 'đ/bông' : 'Chưa cập nhật' }}</div>
                        </div>
                        @if($loHang->gia_nhap)
                        <div class="col-md-3">
                            <div class="info-label">Tổng tiền nhập</div>
                            <div class="info-value" style="color: var(--sunflower-orange);">{{ number_format($loHang->gia_nhap * $loHang->soluong_nhap) }}đ</div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="info-label">Ngày nhập kho</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($loHang->ngaynhap)->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Hạn sử dụng</div>
                            <div class="info-value {{ $hetHan ? 'text-danger' : '' }}">
                                {{ \Carbon\Carbon::parse($loHang->ngayhethan)->format('d/m/Y') }}
                                @if($hetHan) <i class="fa-solid fa-circle-exclamation ms-1"></i> @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Nhà cung cấp</div>
                            <div class="info-value">{{ $loHang->nhacungcap ?: 'Chưa cập nhật' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Người nhập</div>
                            <div class="info-value">{{ $loHang->nhanvien->tennv ?? $loHang->manv }}</div>
                        </div>
                    </div>

                    @if($loHang->ghichu)
                    <hr>
                    <div class="info-label mb-1">Ghi chú</div>
                    <div class="bg-light rounded p-3" style="font-size: 0.9rem;">{{ $loHang->ghichu }}</div>
                    @endif

                    {{-- Thanh tiến trình --}}
                    @php
                        $phanTram = $loHang->soluong_nhap > 0 ? round(($loHang->soluong_ton / $loHang->soluong_nhap) * 100) : 0;
                        $barClass = $hetHang ? 'empty' : ($phanTram <= 20 ? 'low' : ($phanTram <= 50 ? 'medium' : 'high'));
                    @endphp
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="info-label mb-0">Tỷ lệ tồn kho</span>
                        <span class="fw-bold">{{ $phanTram }}%</span>
                    </div>
                    <div class="stock-progress-lg">
                        <div class="bar {{ $barClass }}" style="width: {{ max($phanTram, 2) }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted">0</small>
                        <small class="text-muted">{{ number_format($loHang->soluong_nhap) }}</small>
                    </div>
                </div>
            </div>

            {{-- Timeline lịch sử --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                        <i class="fa-solid fa-clock-rotate-left me-2"></i> Lịch sử hoạt động
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="timeline">
                        <div class="timeline-item type-import">
                            <div class="timeline-date">{{ \Carbon\Carbon::parse($loHang->ngaynhap)->format('d/m/Y') }}</div>
                            <div class="timeline-content">
                                <strong style="color: var(--sunflower-orange);"><i class="fa-solid fa-box-open me-1"></i> Nhập kho</strong>
                                <div class="mt-1">
                                    Nhập <strong>{{ number_format($loHang->soluong_nhap) }} bông</strong> —
                                    bởi <strong>{{ $loHang->nhanvien->tennv ?? $loHang->manv }}</strong>
                                    @if($loHang->gia_nhap) — Giá: <strong>{{ number_format($loHang->gia_nhap) }}đ/bông</strong> @endif
                                </div>
                            </div>
                        </div>

                        @forelse($loHang->phieuhuyhangs as $phieu)
                        <div class="timeline-item type-destroy">
                            <div class="timeline-date">{{ \Carbon\Carbon::parse($phieu->ngayhuy)->format('d/m/Y') }}</div>
                            <div class="timeline-content type-destroy">
                                <strong class="text-danger"><i class="fa-solid fa-trash-can me-1"></i> Hủy hàng — {{ $phieu->maphieu }}</strong>
                                <div class="mt-1">
                                    Hủy <strong class="text-danger">-{{ number_format($phieu->soluong_huy) }} bông</strong> —
                                    bởi <strong>{{ $phieu->nhanvien->tennv ?? $phieu->manv }}</strong>
                                </div>
                                <div class="mt-1 text-muted" style="font-size: 0.83rem;">
                                    <i class="fa-solid fa-comment me-1"></i> {{ $phieu->lydo }}
                                </div>
                            </div>
                        </div>
                        @empty
                        @endforelse
                    </div>

                    @if($loHang->phieuhuyhangs->isEmpty())
                    <div class="text-center text-muted py-3">
                        <i class="fa-solid fa-check-circle me-1 text-success"></i> Chưa có lần hủy hàng nào cho lô này
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ====== CỘT PHẢI ====== --}}
        <div class="col-lg-4 mb-4">
            {{-- Sản phẩm --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-seedling me-2"></i>Sản phẩm liên kết</h6>
                </div>
                <div class="card-body">
                    @php
                        $sp = $loHang->sanpham;
                        $imgUrl = asset('images/bg-sunflower.jpg');
                        if ($sp && !empty($sp->hinhanh)) {
                            $imgUrl = str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : asset('storage/' . ltrim($sp->hinhanh, '/'));
                        }
                    @endphp
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $imgUrl }}" class="rounded shadow-sm" style="width: 70px; height: 70px; object-fit: cover;" alt="">
                        <div>
                            <h6 class="fw-bold mb-1">{{ $sp->tensp ?? 'N/A' }}</h6>
                            <span class="text-muted small">{{ $sp->masp ?? '' }}</span>
                        </div>
                    </div>
                    @if($sp)
                    <hr>
                    <p class="mb-2"><strong>Giá bán:</strong> <span class="text-danger fw-bold">{{ number_format($sp->giakm ?: $sp->giaban) }} ₫</span></p>
                    <p class="mb-2"><strong>Danh mục:</strong> {{ $sp->danhmuc->tendm ?? 'N/A' }}</p>
                    @if($loHang->gia_nhap && $sp->giaban > 0)
                        @php
                            $giaBan = $sp->giakm ?: $sp->giaban;
                            $loiNhuan = $giaBan - $loHang->gia_nhap;
                            $phanTramLN = $giaBan > 0 ? round(($loiNhuan / $giaBan) * 100, 1) : 0;
                        @endphp
                        <p class="mb-0"><strong>Biên lợi nhuận:</strong>
                            <span class="fw-bold {{ $loiNhuan > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $phanTramLN }}% ({{ number_format($loiNhuan) }}đ)
                            </span>
                        </p>
                    @endif
                    @endif
                </div>
            </div>

            {{-- Hành động --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-bolt me-2"></i>Hành động</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.lohang.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Về danh sách
                        </a>
                        @if($loHang->soluong_ton > 0)
                        <a href="{{ route('admin.phieuhuyhang.create') }}" class="btn btn-outline-danger">
                            <i class="fa-solid fa-trash-can me-1"></i> Lập phiếu hủy
                        </a>
                        @endif
                        <a href="{{ route('admin.lohang.create') }}" class="btn text-white" style="background-color: var(--sunflower-orange);">
                            <i class="fa-solid fa-plus me-1"></i> Nhập lô hoa mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
