@extends('layouts.admin')

@section('title', 'Sửa Thông Tin Nhân Viên')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cập nhật thông tin: {{ $nhanvien->hoten }} ({{ $nhanvien->manv }})</h6>
                </div>
                
                <div class="card-body">
                    <form action="{{ route('admin.nhanvien.update', $nhanvien->manv) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" name="hoten" class="form-control @error('hoten') is-invalid @enderror" value="{{ old('hoten', $nhanvien->hoten) }}" required>
                                @error('hoten') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $nhanvien->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt" class="form-control @error('sdt') is-invalid @enderror" value="{{ old('sdt', $nhanvien->sdt) }}" required>
                                @error('sdt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Ngày sinh <span class="text-danger">*</span></label>
                                <input type="date" name="ngaysinh" class="form-control @error('ngaysinh') is-invalid @enderror" value="{{ old('ngaysinh', $nhanvien->ngaysinh) }}" required>
                                @error('ngaysinh') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lương cơ bản (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" name="luong" class="form-control @error('luong') is-invalid @enderror" value="{{ old('luong', round($nhanvien->luong)) }}" required min="0">
                                @error('luong') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Bỏ trống nếu không đổi mật khẩu">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-bold">Quản lý trực tiếp (Tùy chọn)</label>
                                <select name="maquanly" class="form-select @error('maquanly') is-invalid @enderror">
                                    <option value="">-- Không có quản lý trực tiếp --</option>
                                    @foreach($quanlys as $ql)
                                        <option value="{{ $ql->manv }}" {{ old('maquanly', $nhanvien->maquanly) == $ql->manv ? 'selected' : '' }}>
                                            {{ $ql->hoten }} - {{ $ql->manv }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('maquanly') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.nhanvien.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Hủy
                            </a>
                            <button type="submit" class="btn btn-info text-white">
                                <i class="fas fa-save"></i> Cập nhật
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection