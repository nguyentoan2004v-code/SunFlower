@extends('layouts.admin')

@section('title', 'Quản lý Phiếu Nhập Kho')
@section('page_title', 'DANH SÁCH PHIẾU NHẬP KHO')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 fw-bold text-primary">
                <i class="fa-solid fa-box-open me-2"></i> Phiếu Nhập Kho Nguyên Liệu
            </h5>
            <a href="{{ route('admin.phieunhapkho.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Tạo Phiếu Nhập Mới
            </a>
        </div>

        <div class="card-body border-bottom py-3 bg-light">
            <form method="GET" action="{{ route('admin.phieunhapkho.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Mã Phiếu</label>
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm mã phiếu..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Trạng thái</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="Nháp" {{ request('status') == 'Nháp' ? 'selected' : '' }}>Nháp</option>
                        <option value="Hoàn thành" {{ request('status') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="Đã hủy" {{ request('status') == 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary mt-4">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.phieunhapkho.index') }}" class="btn btn-sm btn-outline-secondary mt-4">Xóa lọc</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Mã Phiếu</th>
                            <th>Thời gian lập</th>
                            <th>Nhà cung cấp</th>
                            <th>Người lập</th>
                            <th class="text-end">Tổng tiền</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($phieunhaps as $pn)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $pn->maphieu }}</td>
                                <td>{{ $pn->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $pn->nhaCungCap->ten_ncc ?? 'N/A' }}</td>
                                <td>{{ $pn->nhanVien->hoten ?? $pn->manv }}</td>
                                <td class="text-end fw-bold text-danger">{{ number_format($pn->tongtien, 0, ',', '.') }} đ</td>
                                <td class="text-center">
                                    @if($pn->trangthai == 'Nháp')
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen me-1"></i> Nháp</span>
                                    @elseif($pn->trangthai == 'Hoàn thành')
                                        <span class="badge bg-success"><i class="fa-solid fa-check-double me-1"></i> Hoàn thành</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fa-solid fa-ban me-1"></i> Đã hủy</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.phieunhapkho.show', $pn->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chưa có phiếu nhập kho nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($phieunhaps->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                {{ $phieunhaps->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
