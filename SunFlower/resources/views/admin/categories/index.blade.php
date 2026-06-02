@extends('layouts.admin')

@section('title', 'Quản lý Danh mục')
@section('page_title', 'DANH SÁCH DANH MỤC')

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE CHO BẢNG VÀ PHÂN TRANG
       ========================================== */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white, [data-bs-theme="dark"] .card-footer.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; border-top: 1px solid #373b3e !important; }
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
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-list me-2"></i> Các danh mục sản phẩm
            </h5>
            <a href="{{ route('admin.categories.create') }}" class="btn text-white" style="background-color: var(--sunflower-orange);">
                <i class="fa-solid fa-plus"></i> Thêm Danh mục
            </a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã DM</th>
                            <th>Hình ảnh</th>
                            <th>Tên danh mục</th>
                            <th>Số lượng SP</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $dm)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $dm->madm }}</td>
                            <td>
                                @php
                                    $dmImg = asset('images/bg-sunflower.jpg');
                                    if(!empty($dm->hinhanh)){
                                        $dmImg = str_starts_with($dm->hinhanh, 'http') ? $dm->hinhanh : asset('storage/' . ltrim($dm->hinhanh, '/'));
                                    }
                                @endphp
                                <img src="{{ $dmImg }}" 
                                    class="img-thumbnail shadow-sm rounded" 
                                    style="width: 60px; height: 60px; object-fit: cover;" 
                                    alt="{{ $dm->tendm }}">
                            </td>
                            <td class="fw-medium text-dark">{{ $dm->tendm }}</td>
                            <td>
                                <span class="badge bg-info text-dark rounded-pill px-3">{{ $dm->sanphams_count ?? 0 }} sản phẩm</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.categories.edit', $dm->madm) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen"></i> Sửa
                                </a>
                                
                                <form action="{{ route('admin.categories.destroy', $dm->madm) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
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
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-folder-open fa-2x mb-2"></i><br>
                                Chưa có danh mục nào!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($categories->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection