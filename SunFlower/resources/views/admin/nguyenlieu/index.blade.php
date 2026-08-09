@extends('layouts.admin')

@section('title', 'Quản lý Nguyên liệu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0"><i class="fa-solid fa-leaf me-2" style="color: var(--sunflower-orange);"></i>Quản lý Nguyên liệu</h2>
    <a href="{{ route('admin.nguyenlieu.create') }}" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Thêm nguyên liệu</a>
</div>

{{-- Thống kê --}}
<div class="row mb-4 g-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-primary">{{ number_format($stats['tong_nguyen_lieu']) }}</div>
                <small class="text-muted">Tổng nguyên liệu</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-danger">{{ number_format($stats['het_hang']) }}</div>
                <small class="text-muted">Hết hàng</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="fs-3 fw-bold text-warning">{{ number_format($stats['sap_het']) }}</div>
                <small class="text-muted">Sắp hết (≤ 10)</small>
            </div>
        </div>
    </div>
</div>

{{-- Bộ lọc --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" name="search" class="form-control" placeholder="Tên nguyên liệu..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Trạng thái</label>
                <select name="trang_thai" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="con_hang" {{ request('trang_thai') == 'con_hang' ? 'selected' : '' }}>Còn hàng</option>
                    <option value="sap_het" {{ request('trang_thai') == 'sap_het' ? 'selected' : '' }}>Sắp hết</option>
                    <option value="het_hang" {{ request('trang_thai') == 'het_hang' ? 'selected' : '' }}>Hết hàng</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-fill"><i class="fa-solid fa-search me-1"></i> Lọc</button>
                <a href="{{ route('admin.nguyenlieu.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

{{-- Bảng dữ liệu --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">#</th>
                        <th>Tên nguyên liệu</th>
                        <th class="text-center">Đơn vị</th>
                        <th class="text-center">Tồn thực tế</th>
                        <th class="text-center">Đang giữ</th>
                        <th class="text-center">Khả dụng</th>
                        <th class="text-center">Giá vốn</th>
                        <th class="text-center" style="width: 150px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($nguyenlieus as $index => $nl)
                        @php
                            $khaDung = max(0, $nl->tonkho_thucte - $nl->tonkho_datruoc);
                            $isHetHang = $khaDung <= 0;
                            $isSapHet = !$isHetHang && $khaDung <= 10;
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $nguyenlieus->firstItem() + $index }}</td>
                            <td class="fw-semibold">
                                {{ $nl->ten_nl }}
                                @if($isHetHang)
                                    <span class="badge bg-danger ms-1">Hết hàng</span>
                                @elseif($isSapHet)
                                    <span class="badge bg-warning text-dark ms-1">Sắp hết</span>
                                @endif
                            </td>
                            <td class="text-center">{{ $nl->dvt }}</td>
                            <td class="text-center fw-bold">{{ number_format($nl->tonkho_thucte) }}</td>
                            <td class="text-center text-warning fw-bold">{{ number_format($nl->tonkho_datruoc) }}</td>
                            <td class="text-center {{ $isHetHang ? 'text-danger' : 'text-success' }} fw-bold">{{ number_format($khaDung) }}</td>
                            <td class="text-center">{{ number_format($nl->gia_von, 0, ',', '.') }}đ</td>
                            <td class="text-center">
                                <a href="{{ route('admin.nguyenlieu.edit', $nl->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.nguyenlieu.destroy', $nl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa nguyên liệu này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có nguyên liệu nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center mt-3">{{ $nguyenlieus->links() }}</div>
@endsection
