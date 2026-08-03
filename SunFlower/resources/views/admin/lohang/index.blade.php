@extends('layouts.admin')

@section('title', 'Quản lý Lô Hàng (Nhập Kho)')
@section('page_title', 'QUẢN LÝ LÔ HÀNG (NHẬP KHO)')

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE CHO QUẢN LÝ LÔ HÀNG
       ========================================== */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-footer.bg-white { background-color: #212529 !important; border-top: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .text-dark { color: #e9ecef !important; }

    /* CSS Table */
    [data-bs-theme="dark"] .table { color: #e9ecef !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.05) !important; }

    /* CSS Phân trang */
    [data-bs-theme="dark"] .pagination .page-link { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .pagination .page-item.active .page-link { background-color: var(--sunflower-orange) !important; border-color: var(--sunflower-orange) !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .pagination .page-link:hover { background-color: #373b3e !important; color: #ffffff !important; }

    /* Stat cards dark */
    [data-bs-theme="dark"] .stat-card { background-color: #212529 !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .stat-card .stat-number { color: #e9ecef !important; }

    /* Filter dark */
    [data-bs-theme="dark"] .filter-card .form-control,
    [data-bs-theme="dark"] .filter-card .form-select { background-color: #2c3034 !important; border-color: #495057 !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .filter-card .form-control:focus,
    [data-bs-theme="dark"] .filter-card .form-select:focus { border-color: var(--sunflower-orange) !important; box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25) !important; }

    /* ==========================================
       STAT CARDS
       ========================================== */
    .stat-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 18px 20px;
        background: #fff;
        transition: box-shadow 0.2s;
    }
    .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-card .stat-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .stat-card .stat-number { font-size: 1.6rem; font-weight: 800; color: #2d3748; }
    .stat-card .stat-label { font-size: 0.8rem; font-weight: 600; color: #6c757d; }
    .stat-card.bl-primary { border-left: 4px solid var(--sunflower-orange); }
    .stat-card.bl-success { border-left: 4px solid #198754; }
    .stat-card.bl-warning { border-left: 4px solid #ffc107; }
    .stat-card.bl-danger  { border-left: 4px solid #dc3545; }
    .stat-card .stat-icon.ic-primary { background: #fff3e0; color: var(--sunflower-orange); }
    .stat-card .stat-icon.ic-success { background: #d4edda; color: #198754; }
    .stat-card .stat-icon.ic-warning { background: #fff3cd; color: #ffc107; }
    .stat-card .stat-icon.ic-danger  { background: #f8d7da; color: #dc3545; }

    /* Badge trạng thái */
    .badge-status { font-size: 0.75rem; font-weight: 600; padding: 4px 10px; border-radius: 50px; }

    /* Progress bar tồn kho */
    .stock-progress { height: 6px; border-radius: 3px; background: #e9ecef; overflow: hidden; min-width: 60px; }
    .stock-progress .bar { height: 100%; border-radius: 3px; }
    .stock-progress .bar.high { background: #198754; }
    .stock-progress .bar.medium { background: #ffc107; }
    .stock-progress .bar.low { background: #dc3545; }
    .stock-progress .bar.empty { background: #dee2e6; width: 100% !important; }
    [data-bs-theme="dark"] .stock-progress { background: #373b3e; }

    /* Ảnh thumb */
    .product-thumb { width: 40px; height: 40px; border-radius: 6px; object-fit: cover; }

    /* Hàng hết hạn / sắp hết hạn */
    .row-expired td { background-color: #fff5f5 !important; }
    .row-warning td { background-color: #fffbeb !important; }
    [data-bs-theme="dark"] .row-expired td { background-color: rgba(220,53,69,0.07) !important; }
    [data-bs-theme="dark"] .row-warning td { background-color: rgba(255,193,7,0.05) !important; }
</style>

<div class="container-fluid mt-3">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" id="auto-dismiss-alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ====== STAT CARDS ====== --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bl-primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Tổng lô hàng</div>
                        <div class="stat-number">{{ number_format($stats['tong_lo']) }}</div>
                    </div>
                    <div class="stat-icon ic-primary"><i class="fa-solid fa-boxes-stacked"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bl-success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Tổng tồn kho</div>
                        <div class="stat-number">{{ number_format($stats['tong_ton']) }}</div>
                    </div>
                    <div class="stat-icon ic-success"><i class="fa-solid fa-seedling"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bl-warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Sắp hết hạn</div>
                        <div class="stat-number">{{ number_format($stats['sap_het_han']) }}</div>
                    </div>
                    <div class="stat-icon ic-warning"><i class="fa-solid fa-clock"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card bl-danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="stat-label">Đã hết hàng</div>
                        <div class="stat-number">{{ number_format($stats['het_hang']) }}</div>
                    </div>
                    <div class="stat-icon ic-danger"><i class="fa-solid fa-box-open"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ====== CẢNH BÁO LÔ HÀNG HẾT HẠN CẦN XỬ LÝ ====== --}}
    @if(($stats['can_xu_ly'] ?? 0) > 0)
        <div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" role="alert" style="border-left: 5px solid #dc3545;">
            <div class="me-3" style="font-size: 2rem;">
                <i class="fa-solid fa-triangle-exclamation text-danger"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="alert-heading fw-bold mb-1">
                    <i class="fa-solid fa-exclamation-circle me-1"></i>
                    Có {{ $stats['can_xu_ly'] }} lô hàng đã hết hạn nhưng còn tồn kho!
                </h6>
                <p class="mb-0 small">
                    Các lô hàng này cần được nhân viên kiểm tra và <strong>lập phiếu hủy thủ công</strong> qua chức năng 
                    <a href="{{ route('admin.phieuhuyhang.create') }}" class="alert-link fw-bold">Lập phiếu hủy hàng</a>.
                    Hoặc <a href="{{ route('admin.lohang.index', ['trang_thai' => 'het_han']) }}" class="alert-link fw-bold">xem danh sách lô hết hạn</a> để xử lý.
                </p>
            </div>
        </div>
    @endif

    {{-- ====== BỘ LỌC ====== --}}
    <div class="card shadow-sm border-0 mb-4 filter-card">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.lohang.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-muted mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Mã lô, tên sản phẩm..." value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <label class="form-label small fw-bold text-muted mb-1">Sản phẩm</label>
                        <select name="masp" class="form-select form-select-sm">
                            <option value="">Tất cả</option>
                            @foreach($sanPhams as $sp)
                                <option value="{{ $sp->masp }}" {{ request('masp') == $sp->masp ? 'selected' : '' }}>{{ $sp->tensp }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">Từ ngày</label>
                        <input type="date" name="tu_ngay" class="form-control form-control-sm" value="{{ request('tu_ngay') }}">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">Đến ngày</label>
                        <input type="date" name="den_ngay" class="form-control form-control-sm" value="{{ request('den_ngay') }}">
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <label class="form-label small fw-bold text-muted mb-1">Trạng thái</label>
                        <select name="trang_thai" class="form-select form-select-sm">
                            <option value="">Tất cả</option>
                            <option value="con_hang" {{ request('trang_thai') == 'con_hang' ? 'selected' : '' }}>Còn hàng</option>
                            <option value="ton_thap" {{ request('trang_thai') == 'ton_thap' ? 'selected' : '' }}>Tồn thấp</option>
                            <option value="het_hang" {{ request('trang_thai') == 'het_hang' ? 'selected' : '' }}>Hết hàng</option>
                            <option value="sap_het_han" {{ request('trang_thai') == 'sap_het_han' ? 'selected' : '' }}>Sắp hết hạn</option>
                            <option value="het_han" {{ request('trang_thai') == 'het_han' ? 'selected' : '' }}>Hết hạn</option>
                        </select>
                    </div>
                    <div class="col-lg-1 col-md-12 d-flex gap-1">
                        <button type="submit" class="btn btn-sm text-white" style="background-color: var(--sunflower-orange);">
                            <i class="fa-solid fa-search"></i>
                        </button>
                        <a href="{{ route('admin.lohang.index') }}" class="btn btn-sm btn-outline-secondary" title="Xóa bộ lọc">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ====== BẢNG DỮ LIỆU ====== --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-warehouse me-2"></i> Danh sách Lô hàng nhập kho
            </h5>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.phieuhuyhang.create') }}" class="btn btn-sm btn-outline-danger shadow-sm">
                    <i class="fa-solid fa-trash-can me-1"></i> Lập phiếu hủy
                </a>
                <a href="{{ route('admin.lohang.create') }}" class="btn btn-sm text-white shadow-sm" style="background-color: var(--sunflower-orange);">
                    <i class="fa-solid fa-plus me-1"></i> Nhập lô hoa mới
                </a>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã Lô</th>
                            <th>Sản Phẩm</th>
                            <th class="text-center">SL Nhập</th>
                            <th>Tồn Kho</th>
                            <th>Giá Nhập</th>
                            <th>Nhà CC</th>
                            <th>Ngày Nhập</th>
                            <th>HSD</th>
                            <th>Trạng Thái</th>
                            <th>Người Nhập</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loHangs as $lo)
                        @php
                            $today = \Carbon\Carbon::today();
                            $hetHan = \Carbon\Carbon::parse($lo->ngayhethan)->lt($today);
                            $sapHetHan = !$hetHan && \Carbon\Carbon::parse($lo->ngayhethan)->diffInDays($today) <= 3 && $lo->soluong_ton > 0;
                            $hetHang = $lo->soluong_ton <= 0;
                            $tonThap = !$hetHang && $lo->soluong_nhap > 0 && ($lo->soluong_ton / $lo->soluong_nhap) <= 0.2;
                            $phanTramTon = $lo->soluong_nhap > 0 ? round(($lo->soluong_ton / $lo->soluong_nhap) * 100) : 0;

                            $rowClass = '';
                            if ($hetHan) $rowClass = 'row-expired';
                            elseif ($sapHetHan) $rowClass = 'row-warning';

                            $imgUrl = asset('images/bg-sunflower.jpg');
                            if (!empty($lo->sanpham->hinhanh)) {
                                $imgUrl = str_starts_with($lo->sanpham->hinhanh, 'http')
                                    ? $lo->sanpham->hinhanh
                                    : asset('storage/' . ltrim($lo->sanpham->hinhanh, '/'));
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="ps-4 fw-bold text-secondary">{{ $lo->malo }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $imgUrl }}" class="product-thumb rounded shadow-sm me-2" alt="">
                                    <span class="fw-medium">{{ $lo->sanpham->tensp ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="text-center fw-bold">{{ number_format($lo->soluong_nhap) }}</td>
                            <td>
                                <div>
                                    <span class="fw-bold {{ $hetHang ? 'text-danger' : ($tonThap ? 'text-warning' : 'text-success') }}">
                                        {{ number_format($lo->soluong_ton) }}
                                    </span>
                                    <small class="text-muted">/ {{ number_format($lo->soluong_nhap) }}</small>
                                </div>
                                <div class="stock-progress mt-1">
                                    <div class="bar {{ $hetHang ? 'empty' : ($phanTramTon <= 20 ? 'low' : ($phanTramTon <= 50 ? 'medium' : 'high')) }}"
                                         style="width: {{ $hetHang ? 100 : $phanTramTon }}%"></div>
                                </div>
                            </td>
                            <td>
                                @if($lo->gia_nhap)
                                    {{ number_format($lo->gia_nhap) }}đ
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($lo->nhacungcap)
                                    <span class="text-truncate d-inline-block" style="max-width: 100px;" title="{{ $lo->nhacungcap }}">{{ $lo->nhacungcap }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($lo->ngaynhap)->format('d/m/Y') }}</td>
                            <td>
                                @if($hetHan)
                                    <span class="text-danger fw-bold"><i class="fa-solid fa-circle-exclamation me-1"></i>{{ \Carbon\Carbon::parse($lo->ngayhethan)->format('d/m/Y') }}</span>
                                @elseif($sapHetHan)
                                    <span class="text-warning fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>{{ \Carbon\Carbon::parse($lo->ngayhethan)->format('d/m/Y') }}</span>
                                @else
                                    {{ \Carbon\Carbon::parse($lo->ngayhethan)->format('d/m/Y') }}
                                @endif
                            </td>
                            <td>
                                @if($hetHan)
                                    <span class="badge bg-secondary badge-status">Hết hạn</span>
                                @elseif($hetHang)
                                    <span class="badge bg-danger badge-status">Hết hàng</span>
                                @elseif($sapHetHan)
                                    <span class="badge bg-warning text-dark badge-status">Sắp hết hạn</span>
                                @elseif($tonThap)
                                    <span class="badge bg-warning text-dark badge-status">Tồn thấp</span>
                                @else
                                    <span class="badge bg-success badge-status">Còn hàng</span>
                                @endif
                            </td>
                            <td>{{ $lo->nhanvien->tennv ?? $lo->manv }}</td>
                            <td class="text-center" style="white-space: nowrap;">
                                <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                    <a href="{{ route('admin.lohang.show', $lo->malo) }}" class="btn btn-sm text-white" style="background-color: var(--sunflower-orange);">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                Chưa có lô hàng nào!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($loHangs->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
            <span class="text-muted small">
                Hiển thị {{ $loHangs->firstItem() }}–{{ $loHangs->lastItem() }} / {{ $loHangs->total() }} lô hàng
            </span>
            {{ $loHangs->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('auto-dismiss-alert');
        if (alert) {
            setTimeout(() => { bootstrap.Alert.getOrCreateInstance(alert).close(); }, 4000);
        }
    });
</script>
@endsection