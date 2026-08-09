@extends('layouts.admin')

@section('title', 'Lịch sử Biến động Kho')
@section('page_title', 'LỊCH SỬ BIẾN ĐỘNG KHO')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button loai_gd="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <span class="text-muted small">Theo dõi toàn bộ luồng nhập/xuất nguyên liệu</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.phieunhapkho.index') }}" class="btn btn-success btn-sm shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Nhập Kho
            </a>
            <a href="{{ route('admin.inventory.waste.form') }}" class="btn btn-danger btn-sm shadow-sm">
                <i class="fa-solid fa-trash-can me-1"></i> Xuất Hủy
            </a>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3 bg-light border-bottom">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Nguyên liệu</label>
                    <select name="id_nguyen_lieu" class="form-select form-select-sm select2">
                        <option value="">-- Tất cả --</option>
                        @foreach($nguyenlieus as $nl)
                            <option value="{{ $nl->id }}" {{ request('id_nguyen_lieu') == $nl->id ? 'selected' : '' }}>{{ $nl->ten_nl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Loại giao dịch</label>
                    <select name="loai_gd" class="form-select form-select-sm">
                        <option value="">-- Tất cả --</option>
                        <option value="import" {{ request('loai_gd') == 'import' ? 'selected' : '' }}>Nhập kho</option>
                        <option value="waste" {{ request('loai_gd') == 'waste' ? 'selected' : '' }}>Xuất hủy</option>
                        <option value="order_reserve" {{ request('loai_gd') == 'order_reserve' ? 'selected' : '' }}>Giữ đơn</option>
                        <option value="order_complete" {{ request('loai_gd') == 'order_complete' ? 'selected' : '' }}>Xuất bán</option>
                        <option value="order_cancel" {{ request('loai_gd') == 'order_cancel' ? 'selected' : '' }}>Hoàn trả</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Từ ngày</label>
                    <input loai_gd="date" name="tu_ngay" class="form-control form-control-sm" value="{{ request('tu_ngay') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">Đến ngày</label>
                    <input loai_gd="date" name="den_ngay" class="form-control form-control-sm" value="{{ request('den_ngay') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button loai_gd="submit" class="btn btn-primary btn-sm mt-4 px-3 shadow-sm">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.inventory.logs') }}" class="btn btn-outline-secondary btn-sm mt-4 px-3" title="Xóa bộ lọc">
                        Xóa lọc
                    </a>
                </div>
            </form>
        </div>

        {{-- Bảng lịch sử --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center text-muted" style="width: 50px;">#</th>
                            <th>Thời gian</th>
                            <th>Nguyên liệu</th>
                            <th class="text-center">Loại</th>
                            <th class="text-center">Số lượng</th>
                            <th style="width: 35%;">Chi tiết / Ghi chú</th>
                            <th>Người thực hiện</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $index => $log)
                            <tr>
                                <td class="text-center text-muted">{{ $logs->firstItem() + $index }}</td>
                                <td>
                                    {{ $log->created_at->format('d/m/Y') }} <br>
                                    <small class="text-muted">{{ $log->created_at->format('H:i') }}</small>
                                </td>
                                <td class="fw-medium text-dark">
                                    {{ $log->nguyenLieu->ten_nl ?? 'N/A' }}
                                </td>
                                <td class="text-center">
                                    @if($log->loai_gd == 'import')
                                        <span class="badge bg-primary">Nhập kho</span>
                                    @elseif($log->loai_gd == 'waste')
                                        <span class="badge bg-danger">Xuất hủy</span>
                                    @elseif($log->loai_gd == 'order_reserve')
                                        <span class="badge bg-warning text-dark">Giữ hàng</span>
                                    @elseif($log->loai_gd == 'order_complete')
                                        <span class="badge bg-success">Xuất bán</span>
                                    @elseif($log->loai_gd == 'order_cancel')
                                        <span class="badge bg-secondary">Hoàn trả</span>
                                    @else
                                        <span class="badge bg-dark">{{ $log->loai_gd_label }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fs-6 fw-bold {{ $log->soluong > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->soluong > 0 ? '+' : '' }}{{ number_format($log->soluong) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->ghichu)
                                        @php
                                            $baseghichu = $log->ghichu;
                                            $lotDetails = '';
                                            if (preg_match('/\[(.*?)\]/', $log->ghichu, $matches)) {
                                                $lotDetails = $matches[1];
                                                $baseghichu = trim(str_replace('['.$lotDetails.']', '', $log->ghichu));
                                            }
                                        @endphp

                                        <div class="text-muted small">{{ $baseghichu }}</div>
                                        
                                        @if($lotDetails)
                                            <div class="mt-1">
                                                @php $lots = explode(',', $lotDetails); @endphp
                                                @foreach($lots as $l)
                                                    <span class="badge border text-dark fw-normal bg-light font-monospace me-1 mb-1">
                                                        {{ trim($l) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-muted fst-italic small">Không có</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">
                                        {{ $log->nhanvien->hoten ?? 'Hệ thống' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Chưa có dữ liệu biến động kho.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($logs->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5'
        });
    });
</script>
@endsection
