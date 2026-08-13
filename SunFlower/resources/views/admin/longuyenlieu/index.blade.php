@extends('layouts.admin')

@section('title', 'Quản lý Lô Nguyên Liệu')
@section('page_title', 'DANH SÁCH LÔ NGUYÊN LIỆU')

@section('content')
<div class="container-fluid mt-3">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="alert alert-info py-2 shadow-sm border-0 mb-4 d-flex align-items-center">
        <i class="fa-solid fa-circle-info fs-4 me-3 text-info"></i>
        <div>
            <strong>Nguyên lý trừ Tồn kho (Thuật toán FEFO):</strong><br>
            Hệ thống sẽ tự động trừ các Lô nguyên liệu theo thứ tự: <strong>Lô nào hết hạn sớm nhất sẽ bị trừ trước</strong>. Lô không có hạn sử dụng sẽ trừ theo lô cũ trước (FIFO).
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body border-bottom py-3 bg-light">
            <form method="GET" action="{{ route('admin.longuyenlieu.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Lọc theo Nguyên liệu</label>
                    <select name="id_nguyen_lieu" class="form-select form-select-sm select2">
                        <option value="">Tất cả nguyên liệu</option>
                        @foreach($nguyenlieus as $mat)
                            <option value="{{ $mat->id }}" {{ request('id_nguyen_lieu') == $mat->id ? 'selected' : '' }}>
                                {{ $mat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Trạng thái tồn kho</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Tất cả</option>
                        <option value="Còn hàng" {{ request('status', 'Còn hàng') == 'Còn hàng' ? 'selected' : '' }}>Chỉ xem Lô còn hàng (>0)</option>
                        <option value="Hết hàng" {{ request('status') == 'Hết hàng' ? 'selected' : '' }}>Chỉ xem Lô đã hết (Lịch sử)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-primary mt-4">
                        <i class="fa-solid fa-filter me-1"></i> Lọc
                    </button>
                    <a href="{{ route('admin.longuyenlieu.index') }}" class="btn btn-sm btn-outline-secondary mt-4">Xóa lọc</a>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Mã Lô</th>
                            <th>Nguyên liệu</th>
                            <th>Hạn sử dụng (HSD)</th>
                            <th>Trạng thái HSD</th>
                            <th>Tồn còn lại</th>
                            <th>Nhập ban đầu</th>
                            <th>Phiếu nhập nguồn</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lots as $lot)
                            @php
                                $isExpired = false;
                                $isExpiringSoon = false;
                                $daysLeft = null;
                                
                                if ($lot->hsd && $lot->soluong_hientai > 0) {
                                    $expDate = \Carbon\Carbon::parse($lot->hsd);
                                    $now = \Carbon\Carbon::now()->startOfDay();
                                    
                                    if ($expDate->isPast()) {
                                        $isExpired = true;
                                    } else {
                                        $daysLeft = $now->diffInDays($expDate);
                                        if ($daysLeft <= 7) {
                                            $isExpiringSoon = true;
                                        }
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-center font-monospace fw-bold">
                                    <a href="{{ route('admin.longuyenlieu.trace', $lot->id) }}" class="text-primary text-decoration-none" title="Nhấn để xem lịch sử truy vết lô này">
                                        {{ $lot->malo }} <i class="fa-solid fa-arrow-up-right-from-square ms-1 small"></i>
                                    </a>
                                </td>
                                <td class="fw-medium">{{ $lot->nguyenLieu->ten_nl ?? 'N/A' }}</td>
                                <td class="text-center">
                                    {{ $lot->hsd ? \Carbon\Carbon::parse($lot->hsd)->format('d/m/Y') : 'Không có' }}
                                </td>
                                <td class="text-center">
                                    @if($lot->soluong_hientai == 0)
                                        <span class="badge bg-secondary text-white">Đã hết hàng</span>
                                    @elseif(!$lot->hsd)
                                        <span class="badge bg-light text-dark border">Không thời hạn</span>
                                    @elseif($isExpired)
                                        <span class="badge bg-danger text-white"><i class="fa-solid fa-skull-crossbones me-1"></i> Đã Hết Hạn</span>
                                    @elseif($isExpiringSoon)
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i> Sắp hết hạn ({{ $daysLeft }} ngày)</span>
                                    @else
                                        <span class="badge bg-success text-white">Còn tốt ({{ $daysLeft }} ngày)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-bold {{ $lot->soluong_hientai > 0 ? 'text-success' : 'text-muted' }}">
                                        {{ number_format($lot->soluong_hientai) }}
                                    </span>
                                </td>
                                <td class="text-center text-muted">
                                    {{ number_format($lot->soluong_bandau) }}
                                </td>
                                <td class="text-center">
                                    @if($lot->phieuNhap)
                                        <a href="{{ route('admin.phieunhapkho.show', $lot->id_phieu_nhap) }}" class="text-decoration-none">
                                            {{ $lot->phieuNhap->code }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Thao tác
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.longuyenlieu.trace', $lot->id) }}">
                                                    <i class="fa-solid fa-magnifying-glass text-info me-2"></i> Truy vết Lô
                                                </a>
                                            </li>

                                            @if($lot->soluong_hientai > 0)
                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{ route('admin.phieuhuyhang.create', ['lot_id' => $lot->id]) }}">
                                                        <i class="fa-solid fa-file-circle-xmark text-danger me-2"></i> Lập phiếu hủy lô này
                                                    </a>
                                                </li>
                                            @endif
                                            @if($isExpired || $isExpiringSoon)
                                                <li>
                                                    <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" data-bs-target="#extendModal{{ $lot->id }}">
                                                        <i class="fa-regular fa-calendar-plus text-success me-2"></i> Gia hạn HSD
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    

                                    {{-- Modal Gia hạn --}}
                                    @if($isExpired || $isExpiringSoon)
                                    <div class="modal fade" id="extendModal{{ $lot->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content text-start">
                                                <form action="{{ route('admin.longuyenlieu.extend', $lot->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white">
                                                        <h5 class="modal-title"><i class="fa-regular fa-calendar-plus me-2"></i> Gia hạn Hạn sử dụng</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Mã lô: <strong>{{ $lot->malo }}</strong></p>
                                                        <p>HSD cũ: <strong>{{ $lot->hsd ? \Carbon\Carbon::parse($lot->hsd)->format('d/m/Y') : 'Không có' }}</strong></p>
                                                        <hr>
                                                        <div class="mb-3">
                                                            <label class="form-label">Hạn sử dụng mới</label>
                                                            <input type="date" name="new_hsd" class="form-control" value="{{ $lot->hsd }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-success">Cập nhật HSD</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy Lô nguyên liệu nào phù hợp.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($lots->hasPages())
            <div class="card-footer bg-white border-top py-3 d-flex justify-content-center">
                {{ $lots->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

{{-- Khởi tạo Select2 cho form lọc đẹp hơn nếu có nhiều nguyên liệu --}}
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
