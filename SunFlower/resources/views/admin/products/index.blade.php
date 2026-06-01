@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('page_title', 'DANH SÁCH SẢN PHẨM')

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE (Không ảnh hưởng Light Mode)
       ========================================== */
    
    /* 1. Nền Card và Header */
    [data-bs-theme="dark"] .card {
        background-color: #212529 !important;
        border: 1px solid #373b3e !important;
    }
    [data-bs-theme="dark"] .card-header.bg-white {
        background-color: #2c3034 !important;
        border-bottom: 1px solid #373b3e !important;
    }

    /* 2. Màu chữ tiêu đề và Label */
    [data-bs-theme="dark"] .text-dark,
    [data-bs-theme="dark"] .text-primary,
    [data-bs-theme="dark"] .form-label {
        color: #e9ecef !important;
    }
    
    /* 3. Khung Preview Hình ảnh (bỏ nền sáng) */
    [data-bs-theme="dark"] .bg-light {
        background-color: #2c3034 !important;
        border-color: #495057 !important;
        color: #e9ecef !important;
    }
    
    /* 4. Các thẻ Input / Textarea / Select */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #2c3034 !important;
        border-color: #495057 !important;
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        background-color: #2c3034 !important;
        border-color: var(--sunflower-orange) !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25) !important;
    }
    [data-bs-theme="dark"] .form-control[readonly] {
        background-color: #1a1d20 !important;
        color: #adb5bd !important;
    }

    /* 5. Nút bấm (Buttons) */
    [data-bs-theme="dark"] .btn-light {
        background-color: #343a40 !important;
        color: #dee2e6 !important;
        border-color: #495057 !important;
    }
    [data-bs-theme="dark"] .btn-light:hover {
        background-color: #495057 !important;
        color: #ffffff !important;
    }

    /* 6. CKEditor Dark Mode cơ bản (Ép màu viền) */
    [data-bs-theme="dark"] .cke_chrome {
        border-color: #495057 !important;
    }

    /* 7. CSS Bảng (Table) cho Dark Mode */
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
        background-color: rgba(255, 255, 255, 0.05) !important; /* Hiệu ứng hover cho hàng */
    }

    /* 8. Footer và Phân trang (Pagination) */
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
            <button type="button" class="btn-close" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-boxes-stacked me-2"></i> Danh sách sản phẩm
            </h5>
            <a href="{{ route('admin.products.create') }}" class="btn text-white shadow-sm" style="background-color: var(--sunflower-orange);">
                <i class="fa-solid fa-plus me-1"></i> Thêm sản phẩm mới
            </a>
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
                            <th class="text-center" >Tổng Tồn Kho</th>
                            <th>Giá bán</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $sp)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $sp->masp }}</td>
                            <td>
                                <img src="{{ route('product.image', $sp->masp) }}" class="rounded shadow-sm" style="width:60px; height:60px; object-fit:cover;">
                            </td>
                            <td class="fw-medium">{{ $sp->tensp }} </td>
                            <td>{{ $sp->danhmuc->tendm ?? 'N/A' }} </td>
                            
                            <td class="text-center {{ ($sp->lohangs_sum_soluong_ton ?? 0) == 0 ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' }}">
                                {{ number_format($sp->lohangs_sum_soluong_ton ?? 0) }}
                            </td>
                            <td class="text-danger fw-bold">{{ number_format($sp->giaban, 0, ',', '.') }} ₫</td>
                            
                            <td class="text-center">
                                <a href="{{ route('admin.products.edit', $sp->masp) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                            
                                
                                <form action="{{ route('admin.products.destroy', $sp->masp) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có sản phẩm nào!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- PHẦN THÊM MỚI: Hiển thị các nút chuyển trang --}}
        @if($products->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection