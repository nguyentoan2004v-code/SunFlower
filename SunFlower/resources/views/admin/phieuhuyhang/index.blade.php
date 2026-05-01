@extends('layouts.admin')

@section('title', 'Lịch Sử Hủy Hàng')

@section('content')
<div class="container-fluid mt-3">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold text-danger">
                <i class="fa-solid fa-trash-can me-2"></i> Lịch sử Phiếu Hủy Hàng
            </h5>
            <div>
                <a href="{{ route('admin.lohang.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm me-2">
                    <i class="fa-solid fa-box-open me-1"></i> Về kho hàng
                </a>
                <a href="{{ route('admin.phieuhuyhang.create') }}" class="btn btn-danger btn-sm shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Lập phiếu hủy mới
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã Phiếu</th>
                            <th>Mã Lô</th>
                            <th>Sản Phẩm (Hoa)</th>
                            <th class="text-center">Số Lượng Hủy</th>
                            <th>Lý do</th>
                            <th>Người hủy</th>
                            <th>Ngày hủy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($phieuHuys as $phieu)
                        <tr>
                            <td class="ps-4 fw-bold text-danger">{{ $phieu->maphieu }}</td>
                            <td class="fw-medium text-secondary">{{ $phieu->malo }}</td>
                            <td>
                                @if($phieu->sanpham)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ route('product.image', $phieu->masp) }}" class="rounded shadow-sm me-2" style="width:40px; height:40px; object-fit:cover;">
                                        <span>{{ $phieu->sanpham->tensp }}</span>
                                    </div>
                                @else
                                    <span class="text-muted">Không xác định ({{ $phieu->masp }})</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold fs-5 text-danger">
                                -{{ number_format($phieu->soluong_huy) }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-wrap text-start" style="max-width: 200px;">
                                    {{ $phieu->lydo }}
                                </span>
                            </td>
                            <td>{{ $phieu->nhanvien->hoten ?? $phieu->manv }}</td>
                            <td>{{ \Carbon\Carbon::parse($phieu->ngayhuy)->format('d/m/Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-1 d-block mb-3 opacity-50"></i>
                                Chưa có lịch sử hủy hàng nào!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection