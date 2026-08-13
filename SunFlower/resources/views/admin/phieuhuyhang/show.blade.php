@extends('layouts.admin')

@section('title', 'Chi Tiết Phiếu Hủy Hàng')
@section('page_title', 'CHI TIẾT PHIẾU HỦY HÀNG')

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

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-bottom text-uppercase fw-bold text-muted">
                    <i class="fa-solid fa-file-invoice me-1"></i> Thông Tin Phiếu
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 40%">Mã phiếu:</td>
                                <td class="fw-bold text-primary fs-5">{{ $phieuHuy->ma_phieu_huy }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày lập:</td>
                                <td class="fw-medium">{{ $phieuHuy->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Người lập:</td>
                                <td class="fw-medium">{{ $phieuHuy->nguoiLap->hoten ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Trạng thái:</td>
                                <td>
                                    @if($phieuHuy->trang_thai == 'Chờ duyệt')
                                        <span class="badge bg-warning text-dark px-3 py-2 fs-6">Chờ duyệt</span>
                                    @elseif($phieuHuy->trang_thai == 'Đã duyệt')
                                        <span class="badge bg-success px-3 py-2 fs-6">Đã duyệt</span>
                                    @elseif($phieuHuy->trang_thai == 'Từ chối')
                                        <span class="badge bg-danger px-3 py-2 fs-6">Từ chối</span>
                                    @endif
                                </td>
                            </tr>
                            @if($phieuHuy->trang_thai != 'Chờ duyệt')
                            <tr>
                                <td class="text-muted">Người duyệt:</td>
                                <td class="fw-medium">{{ $phieuHuy->nguoiDuyet->hoten ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Ngày duyệt:</td>
                                <td class="fw-medium">{{ $phieuHuy->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    
                    @if($phieuHuy->ghi_chu_chung)
                        <hr class="text-muted">
                        <div class="text-muted small mb-1">Ghi chú chung:</div>
                        <div class="bg-light p-2 rounded border">{{ $phieuHuy->ghi_chu_chung }}</div>
                    @endif
                </div>
                
                {{-- Khu vực duyệt phiếu --}}
                @if($phieuHuy->trang_thai == 'Chờ duyệt')
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex gap-2 justify-content-center">
                        <form action="{{ route('admin.phieuhuyhang.reject', $phieuHuy->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn TỪ CHỐI phiếu hủy này? Số lượng tồn kho sẽ được hoàn lại.');">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger shadow-sm">
                                <i class="fa-solid fa-xmark me-1"></i> Từ chối
                            </button>
                        </form>
                        
                        <form action="{{ route('admin.phieuhuyhang.approve', $phieuHuy->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn DUYỆT phiếu hủy này?');">
                            @csrf
                            <button type="submit" class="btn btn-success shadow-sm px-4">
                                <i class="fa-solid fa-check-double me-1"></i> Duyệt Phiếu
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
            
            <a href="{{ route('admin.phieuhuyhang.index') }}" class="btn btn-light border w-100 shadow-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
            </a>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light border-bottom text-uppercase fw-bold text-muted">
                    <i class="fa-solid fa-list-check me-1"></i> Chi Tiết Hủy
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width: 5%">#</th>
                                    <th>Lô Nguyên Liệu</th>
                                    <th>Nguyên Liệu</th>
                                    <th>Số Lượng Hủy</th>
                                    <th>Lý Do</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($phieuHuy->chiTiet as $index => $ct)
                                    <tr>
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td class="text-center font-monospace fw-bold text-primary">
                                            <a href="{{ route('admin.longuyenlieu.trace', $ct->id_lo_nguyen_lieu) }}" class="text-decoration-none" title="Truy vết lô">
                                                {{ $ct->loNguyenLieu->malo ?? 'N/A' }}
                                            </a>
                                        </td>
                                        <td class="fw-medium">{{ $ct->loNguyenLieu->nguyenLieu->ten_nl ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            <span class="fs-5 fw-bold text-danger">-{{ number_format($ct->so_luong_huy) }}</span>
                                        </td>
                                        <td>{{ $ct->ly_do_chi_tiet }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-end text-muted small py-2">
                    Tổng số mục hủy: <strong>{{ $phieuHuy->chiTiet->count() }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
