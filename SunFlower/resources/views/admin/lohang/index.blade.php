@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Lô Hàng (Nhập Kho)</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.phieuhuyhang.create') }}" class="btn btn-danger shadow-sm">
                <i class="fa-solid fa-trash-can me-1"></i> Lập phiếu hủy
            </a>
            <a href="{{ route('admin.lohang.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nhập lô hoa mới
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã Lô</th>
                            <th>Tên Hoa (Sản phẩm)</th>
                            <th>SL Nhập</th>
                            <th>SL Tồn (Hiện tại)</th>
                            <th>Ngày Nhập</th>
                            <th>Hạn Sử Dụng</th>
                            <th>Người Nhập</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loHangs as $lo)
                        <tr>
                            <td>{{ $lo->malo }}</td>
                            <td>{{ $lo->sanpham->tensp ?? 'N/A' }}</td>
                            <td>{{ $lo->soluong_nhap }}</td>
                            <!-- Highlight những lô sắp hết hoa hoặc đã hết -->
                            <td class="{{ $lo->soluong_ton == 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                {{ $lo->soluong_ton }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($lo->ngaynhap)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($lo->ngayhethan)->format('d/m/Y') }}</td>
                            <td>{{ $lo->nhanvien->tennv ?? $lo->manv }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection