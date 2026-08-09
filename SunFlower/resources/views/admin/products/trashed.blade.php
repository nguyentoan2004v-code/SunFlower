@extends('layouts.admin')

@section('title', 'Sản phẩm đã ẩn')
@section('page_title', 'SẢN PHẨM ĐÃ ẨN')

@section('content')
<style>
    /* Dark Mode cho trang này */
    [data-bs-theme="dark"] .card {
        background-color: #212529 !important;
        border: 1px solid #373b3e !important;
    }
    [data-bs-theme="dark"] .card-header.bg-white {
        background-color: #2c3034 !important;
        border-bottom: 1px solid #373b3e !important;
    }
    [data-bs-theme="dark"] .text-dark,
    [data-bs-theme="dark"] .text-primary,
    [data-bs-theme="dark"] .form-label {
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #2c3034 !important;
        border-color: #495057 !important;
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .table {
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .table-light th {
        background-color: #1a1d20 !important;
        color: #adb5bd !important;
        border-bottom: 2px solid #373b3e !important;
    }
    [data-bs-theme="dark"] .table td,
    [data-bs-theme="dark"] .table th {
        border-color: #373b3e !important;
    }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }
    [data-bs-theme="dark"] .card-footer.bg-white {
        background-color: #212529 !important;
        border-top: 1px solid #373b3e !important;
    }
    [data-bs-theme="dark"] .pagination .page-link {
        background-color: #2c3034 !important;
        border-color: #373b3e !important;
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .pagination .page-item.active .page-link {
        background-color: var(--sunflower-orange) !important;
        border-color: var(--sunflower-orange) !important;
        color: #ffffff !important;
    }
    [data-bs-theme="dark"] .pagination .page-link:hover {
        background-color: #373b3e !important;
        color: #ffffff !important;
    }
</style>
<div class="container-fluid mt-3">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold text-secondary">
                <i class="fa-solid fa-eye-slash me-2"></i> Sản phẩm đã ẩn
            </h5>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-primary shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        {{-- Thanh tìm kiếm --}}
        <div class="card-body border-bottom py-3">
            <form method="GET" action="{{ route('admin.products.trashed') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm theo mã SP hoặc tên sản phẩm..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-1">
                        <button type="submit" class="btn btn-sm text-white" style="background-color: var(--sunflower-orange);">
                            <i class="fa-solid fa-search me-1"></i> Tìm
                        </button>
                        <a href="{{ route('admin.products.trashed') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã SP</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Danh mục</th>
                            <th>Giá bán</th>
                            <th>Ngày ẩn</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trashedProducts as $sp)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $sp->masp }}</td>
                            <td>
                                <img src="{{ str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : asset('storage/' . ltrim($sp->hinhanh, '/')) }}" class="rounded shadow-sm" style="width:60px; height:60px; object-fit:cover; opacity: 0.5;">
                            </td>
                            <td class="fw-medium text-muted"><s>{{ $sp->tensp }}</s></td>
                            <td>{{ $sp->danhmuc->tendm ?? 'N/A' }}</td>
                            <td class="text-danger fw-bold">{{ number_format($sp->giaban, 0, ',', '.') }} ₫</td>
                            <td class="text-muted small">{{ $sp->deleted_at->format('d/m/Y H:i') }}</td>
                            
                            <td class="text-center">
                                <form action="{{ route('admin.products.restore', $sp->masp) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Khôi phục sản phẩm này? Sản phẩm sẽ hiển thị lại trên trang web.');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-check-circle me-1"></i> Không có sản phẩm nào đang bị ẩn!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Phân trang --}}
        @if($trashedProducts->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $trashedProducts->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection
