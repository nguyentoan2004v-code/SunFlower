@extends('layouts.admin')

@section('title', 'Quản lý Mã giảm giá')
@section('page_title', 'DANH SÁCH MÃ GIẢM GIÁ')

@section('content')
<style>
    /* BỔ SUNG DARK MODE VÀ CUSTOM UI */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header, [data-bs-theme="dark"] .card-footer { background-color: #2c3034 !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .text-dark { color: #e9ecef !important; }
    [data-bs-theme="dark"] .text-muted { color: #adb5bd !important; }
    
    [data-bs-theme="dark"] .table { color: #e9ecef !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; border-top: none; }
    [data-bs-theme="dark"] .table td { border-color: #373b3e !important; }
    
    [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; }
    
    /* UI Nâng cấp */
    .table-wrapper { overflow: hidden; border-radius: 0.5rem; }
    .table-light th { font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; padding-top: 15px; padding-bottom: 15px; border-top: none;}
    .voucher-code { letter-spacing: 1px; font-family: monospace; font-size: 1.05rem; }
    .badge-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; border: 1px solid rgba(25, 135, 84, 0.2); }
    .badge-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.2); }
    .badge-soft-warning { background-color: rgba(255, 193, 7, 0.15); color: #b38600; border: 1px solid rgba(255, 193, 7, 0.3); }
    .badge-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; border: 1px solid rgba(220, 53, 69, 0.2); }
    .badge-soft-secondary { background-color: rgba(108, 117, 125, 0.1); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.2); }
    
    [data-bs-theme="dark"] .badge-soft-success { background-color: rgba(25, 135, 84, 0.2); color: #75b798; }
    [data-bs-theme="dark"] .badge-soft-primary { background-color: rgba(13, 110, 253, 0.2); color: #6ea8fe; }
    [data-bs-theme="dark"] .badge-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #ffda6a; }
    
    .btn-action { border-radius: 50px; padding: 0.25rem 0.75rem; font-size: 0.875rem; transition: all 0.2s; }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
</style>

<div class="container-fluid px-4">
    <div class="mb-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center rounded-3" role="alert">
                <i class="fa-solid fa-circle-check fs-4 me-3"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center rounded-3" role="alert">
                <i class="fa-solid fa-triangle-exclamation fs-4 me-3"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                Quản Lý Khuyến Mãi
            </h5>
            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary shadow-sm rounded-pill px-4 py-2 fw-semibold">
                <i class="fa-solid fa-plus me-2"></i>Tạo mã mới
            </a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive table-wrapper border-top">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted">
                        <tr>
                            <th class="ps-4">Mã Voucher</th>
                            <th>Thông tin chương trình</th>
                            <th>Mức giảm</th>
                            <th>Phạm vi</th>
                            <th style="width: 12%">Đã dùng</th>
                            <th>Hiển thị</th>
                            <th>Thời gian</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-end pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vouchers as $vc)
                        <tr>
                            <td class="ps-4">
                                <span class="badge badge-soft-primary voucher-code py-2 px-3">{{ $vc->mavoucher }}</span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $vc->tenvoucher }}</div>
                                <span class="badge bg-light text-muted border">Đơn tối thiểu: {{ number_format($vc->don_min, 0, ',', '.') }}đ</span>
                            </td>
                            <td>
                                @if($vc->loai_giam === 'phan_tram')
                                    <div class="fw-bold text-danger fs-6">{{ (int)$vc->gia_tri_giam }}%</div>
                                    @if($vc->giam_max)
                                        <small class="text-muted"><i class="fa-solid fa-arrow-down-up-across-line me-1"></i>Max: {{ number_format($vc->giam_max, 0, ',', '.') }}đ</small>
                                    @endif
                                @else
                                    <div class="fw-bold text-danger fs-6">{{ number_format($vc->gia_tri_giam, 0, ',', '.') }}đ</div>
                                @endif
                            </td>
                            <td>
                                @if($vc->loai_ap_dung === 'tat_ca')
                                    <span class="badge badge-soft-success"><i class="fa-solid fa-globe me-1"></i>Toàn sàn</span>
                                @else
                                    <span class="badge badge-soft-warning"><i class="fa-solid fa-layer-group me-1"></i>Danh mục</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="fw-semibold text-dark">{{ $vc->da_sudung }}</small>
                                    <small class="text-muted">/ {{ $vc->soluong }}</small>
                                </div>
                                <div class="progress rounded-pill shadow-sm" style="height: 6px;">
                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $vc->soluong > 0 ? ($vc->da_sudung / $vc->soluong) * 100 : 0 }}%"></div>
                                </div>
                            </td>
                            <td>
                                @if($vc->hien_thi === 'cong_khai')
                                    <span class="text-success small fw-semibold"><i class="fa-solid fa-eye me-1"></i>Công khai</span>
                                @else
                                    <span class="text-secondary small fw-semibold"><i class="fa-solid fa-eye-slash me-1"></i>Mã ẩn</span>
                                @endif
                            </td>
                            <td>
                                <div class="small text-dark mb-1"><i class="fa-regular fa-calendar-check text-success me-1"></i> {{ date('d/m/Y H:i', strtotime($vc->ngay_bd)) }}</div>
                                <div class="small text-dark"><i class="fa-regular fa-calendar-xmark text-danger me-1"></i> {{ date('d/m/Y H:i', strtotime($vc->ngay_kt)) }}</div>
                            </td>
                            <td class="text-center">
                                @if($vc->trangthai == 1 && strtotime($vc->ngay_kt) >= time())
                                    <span class="badge badge-soft-success rounded-pill px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i>Hoạt động</span>
                                @elseif($vc->trangthai == 0)
                                    <span class="badge badge-soft-secondary rounded-pill px-3 py-2"><i class="fa-solid fa-lock me-1"></i>Đã khóa</span>
                                @else
                                    <span class="badge badge-soft-danger rounded-pill px-3 py-2"><i class="fa-solid fa-clock me-1"></i>Hết hạn</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.vouchers.edit', $vc->mavoucher) }}" class="btn btn-outline-primary btn-action">
                                        <i class="fa-solid fa-pen"></i> Sửa
                                    </a>
                                    <form action="{{ route('admin.vouchers.destroy', $vc->mavoucher) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa mã giảm giá này? Nếu mã đã được sử dụng, hệ thống khuyến khích nên Tắt trạng thái thay vì Xóa.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-action">
                                            <i class="fa-solid fa-trash-can"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="text-center py-5 my-3">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                        <i class="fa-solid fa-ticket fa-3x text-muted opacity-50"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Chưa có mã giảm giá nào</h5>
                                    <p class="text-muted mb-4">Tạo mã giảm giá mới để thu hút khách hàng và tăng doanh thu cho cửa hàng của bạn.</p>
                                    <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                        <i class="fa-solid fa-plus me-2"></i>Tạo mã giảm giá ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($vouchers->hasPages())
        <div class="card-footer bg-white border-top py-3 d-flex justify-content-center rounded-bottom-4">
            {{ $vouchers->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection