@extends('layouts.admin')

@section('title', 'Quản lý Nhân sự')
@section('page_title', 'DANH SÁCH NHÂN VIÊN')

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE CHO QUẢN LÝ NHÂN SỰ
       ========================================== */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    
    /* CSS Table */
    [data-bs-theme="dark"] .table { color: #e9ecef !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: #373b3e !important; }
    [data-bs-theme="dark"] .table-hover tbody tr:hover td { background-color: rgba(255, 255, 255, 0.05) !important; }

    /* CSS Phân trang */
    [data-bs-theme="dark"] .pagination .page-link { background-color: #2c3034 !important; border-color: #373b3e !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .pagination .page-item.active .page-link { background-color: var(--sunflower-orange, #FF8C00) !important; border-color: var(--sunflower-orange, #FF8C00) !important; color: #ffffff !important; }
    [data-bs-theme="dark"] .pagination .page-link:hover { background-color: #373b3e !important; color: #ffffff !important; }
</style>

<div class="container-fluid mt-4">
    <div class="card shadow-sm border-0 mb-4">
        
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
            <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                <i class="fa-solid fa-users-viewfinder me-2"></i> Danh sách Nhân viên
            </h5>
            <a href="{{ route('admin.nhanvien.create') }}" class="btn text-white shadow-sm" style="background-color: var(--sunflower-orange);">
                <i class="fa-solid fa-plus me-1"></i> Thêm nhân viên
            </a>
        </div>
        
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Mã NV</th>
                            <th>Họ và Tên</th>
                            <th>Email</th>
                            <th>Quản lý trực tiếp</th>
                            <th>Vai trò / Phân quyền</th>
                            <th class="text-center" style="width: 15%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nhanviens as $nv)
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">{{ $nv->manv }}</td>
                                <td class="fw-bold">{{ $nv->hoten }}</td>
                                <td>{{ $nv->email }}</td>
                                <td>
                                    {{-- Kiểm tra xem nhân viên có quản lý không --}}
                                    {{ $nv->quanly ? $nv->quanly->hoten : 'Không có' }}
                                </td>
                                <td>
                                    @if($nv->vaitros->isEmpty())
                                        <span class="badge bg-secondary text-white rounded-pill px-3">Chưa cấp quyền</span>
                                    @else
                                        @foreach($nv->vaitros as $role)
                                            <span class="badge bg-success text-white mb-1 rounded-pill px-3">{{ $role->tenvt }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-center" style="width: 120px;">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.nhanvien.roles', $nv->manv) }}" class="btn btn-warning btn-sm shadow-sm" title="Cấp quyền">
                                            <i class="fas fa-user-shield"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.nhanvien.edit', $nv->manv) }}" class="btn btn-info btn-sm text-white shadow-sm" title="Sửa thông tin">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <form action="{{ route('admin.nhanvien.destroy', $nv->manv) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên {{ $nv->hoten }}? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm shadow-sm" title="Xóa nhân viên">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu nhân viên nào trong hệ thống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($nhanviens->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $nhanviens->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection