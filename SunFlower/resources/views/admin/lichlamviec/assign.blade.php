@extends('layouts.admin')

@section('title', 'Phân Công Nhân Viên')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h5 class="fw-bold text-primary">
            Phân công cho Ca: <span class="text-danger">{{ $lich->maca }}</span> 
            ({{ \Carbon\Carbon::parse($lich->giolam)->format('H:i') }} - {{ \Carbon\Carbon::parse($lich->giotan)->format('H:i') }})
        </h5>
        <a href="{{ route('admin.lichlamviec.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </div>

    <div class="row">
        <!-- Form thêm nhân viên vào Ca -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Giao việc cho Nhân viên</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.lichlamviec.storeAssign', $lich->maca) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn Nhân viên</label>
                            <select name="manv" class="form-select" required>
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($nhanviens as $nv)
                                    <option value="{{ $nv->manv }}">{{ $nv->hoten }} ({{ $nv->manv }})</option>
                                @endforeach
                            </select>
                        </div>
                       
                        <button type="submit" class="btn btn-warning w-100 fw-bold"><i class="fas fa-check"></i> Xác nhận phân công</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bảng hiển thị người đã được phân công -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nhân sự đang phụ trách ca này</h6>
                </div>
                <div class="card-body">
                    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nhân viên</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lich->nhanviens as $nv)
                                    <tr>
                                        <td class="align-middle fw-bold">{{ $nv->hoten }} <br><small class="text-muted">{{ $nv->manv }}</small></td>
                                        <!-- Lấy dữ liệu từ bảng trung gian phancong qua Pivot -->
                                        <td class="align-middle text-primary fw-bold">{{ $nv->pivot->tencongviec }}</td>
                                        <td class="align-middle">{{ $nv->pivot->dacta }}</td>
                                        <td class="text-center align-middle">
                                            <form action="{{ route('admin.lichlamviec.removeAssign', [$lich->maca, $nv->manv]) }}" method="POST" class="m-0" onsubmit="return confirm('Gỡ nhân viên này khỏi ca?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Gỡ khỏi ca"><i class="fas fa-user-minus"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">Ca này hiện chưa có người trực.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection