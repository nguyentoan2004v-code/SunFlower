@extends('layouts.admin')

@section('title', 'Quản lý Phiếu Hủy Hàng')
@section('page_title', 'DANH SÁCH PHIẾU HỦY HÀNG')

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
            <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="text-muted small">
            Quản lý các phiếu hủy nguyên liệu (do hư hỏng, hết hạn,...)
        </div>
        <a href="{{ route('admin.phieuhuyhang.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Lập Phiếu Hủy
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3 bg-light border-bottom">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Trạng thái phiếu</label>
                    <select name="trang_thai" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="Chờ duyệt" {{ request('trang_thai') == 'Chờ duyệt' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="Đã duyệt" {{ request('trang_thai') == 'Đã duyệt' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="Từ chối" {{ request('trang_thai') == 'Từ chối' ? 'selected' : '' }}>Từ chối</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm mt-4 px-3 shadow-sm">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.phieuhuyhang.index') }}" class="btn btn-outline-secondary btn-sm mt-4 px-3" title="Xóa bộ lọc">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-muted" style="width: 50px;">#</th>
                            <th>Mã Phiếu</th>
                            <th>Ngày Lập</th>
                            <th>Người Lập</th>
                            <th>Người Duyệt</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($phieuHuys as $index => $phieu)
                            <tr>
                                <td class="text-center text-muted">{{ $phieuHuys->firstItem() + $index }}</td>
                                <td class="fw-bold text-primary">{{ $phieu->ma_phieu_huy }}</td>
                                <td>
                                    {{ $phieu->created_at->format('d/m/Y') }} <br>
                                    <small class="text-muted">{{ $phieu->created_at->format('H:i') }}</small>
                                </td>
                                <td>{{ $phieu->nguoiLap->hoten ?? 'N/A' }}</td>
                                <td>{{ $phieu->nguoiDuyet->hoten ?? '-' }}</td>
                                <td class="text-center">
                                    @if($phieu->trang_thai == 'Chờ duyệt')
                                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @elseif($phieu->trang_thai == 'Đã duyệt')
                                        <span class="badge bg-success">Đã duyệt</span>
                                    @elseif($phieu->trang_thai == 'Từ chối')
                                        <span class="badge bg-danger">Từ chối</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.phieuhuyhang.show', $phieu->id) }}" class="btn btn-sm btn-info text-white shadow-sm" title="Xem chi tiết">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Không tìm thấy phiếu hủy hàng nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($phieuHuys->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $phieuHuys->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
