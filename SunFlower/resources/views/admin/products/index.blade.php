@extends('layouts.admin')

@section('title', 'Quản lý Sản phẩm')
@section('page_title', 'DANH SÁCH SẢN PHẨM')

@section('content')
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