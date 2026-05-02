@extends('layouts.admin')

@section('title', 'Quản lý Nhân sự')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách Nhân viên</h6>
            <!-- Nút thêm nhân viên mới (nếu bạn phát triển thêm chức năng này) -->
            <a href="{{ route('admin.nhanvien.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Thêm nhân viên
            </a>
        </div>
        <div class="card-body">
            <!-- Hiển thị thông báo thành công -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã NV</th>
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
                                <td class="align-middle">{{ $nv->manv }}</td>
                                <td class="align-middle fw-bold">{{ $nv->hoten }}</td>
                                <td class="align-middle">{{ $nv->email }}</td>
                                <td class="align-middle">
                                    {{-- Kiểm tra xem nhân viên có quản lý không --}}
                                    {{ $nv->quanly ? $nv->quanly->hoten : 'Không có' }}
                                </td>
                                <td class="align-middle">
                                    @if($nv->vaitros->isEmpty())
                                        <span class="badge bg-secondary text-white">Chưa cấp quyền</span>
                                    @else
                                        @foreach($nv->vaitros as $role)
                                            <span class="badge bg-success text-white mb-1">{{ $role->tenvt }}</span>
                                        @endforeach
                                    @endif
                                </td>
                                <td class="text-center align-middle" style="width: 120px;">
    <div class="d-flex justify-content-center gap-1">
        <!-- Nút Phân quyền -->
        <a href="{{ route('admin.nhanvien.roles', $nv->manv) }}" class="btn btn-warning btn-sm" title="Cấp quyền">
            <i class="fas fa-user-shield"></i>
        </a>
        
        <!-- Nút Sửa -->
        <a href="{{ route('admin.nhanvien.edit', $nv->manv) }}" class="btn btn-info btn-sm text-white" title="Sửa thông tin">
            <i class="fas fa-edit"></i>
        </a>
        
        <!-- Nút Xóa (Thêm class m-0 vào form để không bị lệch) -->
        <form action="{{ route('admin.nhanvien.destroy', $nv->manv) }}" method="POST" class="m-0" onsubmit="return confirm('Bạn có chắc chắn muốn xóa nhân viên {{ $nv->hoten }}? Hành động này không thể hoàn tác.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm" title="Xóa nhân viên">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Chưa có dữ liệu nhân viên nào trong hệ thống.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Phân trang -->
            <div class="d-flex justify-content-end mt-3">
                {{ $nhanviens->links() }}
            </div>
        </div>
    </div>
</div>
@endsection