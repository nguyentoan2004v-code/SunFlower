@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('page_title', 'DANH SÁCH SẢN PHẨM')

@section('content')
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
                <i class="fa-solid fa-boxes-stacked me-2"></i> Danh sách sản phẩm
            </h5>
            <a href="{{ route('admin.products.create') }}" class="btn text-white" style="background-color: var(--sunflower-orange);">
                <i class="fa-solid fa-plus"></i> Thêm Sản phẩm
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
                            <th>Giá bán</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $sp)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">{{ $sp->masp }}</td>
                            <td>
                                <img src="{{ route('product.image', $sp->masp) }}" 
                                     class="img-thumbnail shadow-sm rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;" 
                                     alt="Ảnh {{ $sp->tensp }}">
                            </td>
                            <td class="fw-medium">{{ $sp->tensp }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $sp->danhmuc->tendm ?? 'Chưa có' }}</span>
                            </td>
                            <td class="text-danger fw-bold">
                                {{ number_format($sp->giaban, 0, ',', '.') }} ₫
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.products.edit', $sp->masp) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-pen"></i> Sửa
                                </a>
                                
                                <form action="{{ route('admin.products.destroy', $sp->masp) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không? Hành động này không thể hoàn tác!');">
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
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-box-open fa-2x mb-2"></i><br>
                                Chưa có sản phẩm nào trong hệ thống!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($products->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-end">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection