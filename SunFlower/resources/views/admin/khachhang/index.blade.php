@extends('layouts.admin')

@section('title', 'Quản lý Khách hàng')
@section('page_title', 'DANH SÁCH KHÁCH HÀNG')

@section('content')
<style>
    /* BỔ SUNG DARK MODE GIỐNG TRANG NHÂN VIÊN */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .table { color: #e9ecef !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.05) !important; }
    [data-bs-theme="dark"] .pagination .page-link { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .pagination .page-item.active .page-link { background-color: var(--sunflower-orange, #FF8C00) !important; border-color: var(--sunflower-orange, #FF8C00) !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .pagination .page-link:hover { background-color: #373b3e !important; color: #ffffff !important; }
</style>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0 mb-4">
        
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-users me-2"></i> Danh sách Khách hàng
            </h5>
            {{-- Không cần nút Thêm Khách Hàng vì khách sẽ tự đăng ký ngoài trang chủ --}}
        </div>
        
        <div class="card-body">
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mã KH</th>
                            <th>Họ và Tên</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th class="text-center" style="width: 20%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($khachhangs as $kh)
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">{{ $kh->makh }}</td>
                                <td class="fw-bold">
                                    {{ $kh->hoten }}
                                    
                                    {{-- Kiểm tra tiền tố của mã khách hàng --}}
                                    @if(str_starts_with($kh->makh, 'KH'))
                                        <span class="badge bg-success ms-2" style="font-size: 0.7rem;">Thành viên</span>
                                    @elseif(str_starts_with($kh->makh, 'KVL'))
                                        <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">Vãng lai</span>
                                    @endif
                                </td>
                                <td>{{ $kh->sdt }}</td>
                                <td>{{ $kh->diachi }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.khachhang.history', $kh->makh) }}" class="btn btn-success btn-sm shadow-sm" title="Xem lịch sử mua hàng">
                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.khachhang.edit', $kh->makh) }}" class="btn btn-info btn-sm text-white shadow-sm" title="Sửa thông tin">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Nút Reset Password --}}
                                        <form action="{{ route('admin.khachhang.resetPassword', $kh->makh) }}" method="POST" class="m-0" onsubmit="return confirm('Đặt lại mật khẩu cho khách hàng này thành mặc định (password123)?');">
                                            @csrf
                                            <button type="submit" class="btn btn-warning btn-sm text-white shadow-sm" title="Reset mật khẩu">
                                                <i class="fa-solid fa-key"></i>
                                            </button>
                                        </form>
                                        
                                        {{-- Nút Xóa --}}
                                        <form action="{{ route('admin.khachhang.destroy', $kh->makh) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản {{ $kh->hoten }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Xóa tài khoản">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu khách hàng nào trong hệ thống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($khachhangs->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $khachhangs->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection