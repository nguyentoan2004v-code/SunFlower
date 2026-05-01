@extends('layouts.admin')

@section('title', 'Nhập Lô Hoa Mới')

@section('content')
<div class="container-fluid">
    {{-- Nút Quay lại mẫu thanh mảnh --}}
    <div class="mb-3">
        <a href="{{ route('admin.lohang.index') }}" class="text-secondary text-decoration-none shadow-none">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <h1 class="h3 mb-4 text-gray-800">Nhập Lô Hoa Mới</h1>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i> Vui lòng kiểm tra lại dữ liệu nhập vào!
        </div>
    @endif

    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('admin.lohang.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- Mã Lô Hàng --}}
                    <div class="col-md-6 mb-3">
                        <label for="malo" class="form-label fw-bold">Mã Lô Hàng</label>
                        <input type="text" class="form-control bg-light" id="malo" name="malo" value="{{ $newMaLo }}" readonly>
                    </div>

                    {{-- Chọn Sản Phẩm (Đã sửa ID để khớp với Script) --}}
                    <div class="col-md-6 mb-3">
                        <label for="masp" class="form-label fw-bold">Chọn Sản Phẩm (Hoa)</label>
                        <select class="form-control tom-select @error('masp') is-invalid @enderror" id="select-masp" name="masp" required>
                            <option value=""></option>
                            @foreach($sanPhams as $sp)
                                <option value="{{ $sp->masp }}" {{ old('masp') == $sp->masp ? 'selected' : '' }}>
                                    [{{ $sp->masp }}] - {{ $sp->tensp }}
                                </option>
                            @endforeach
                        </select>
                        @error('masp') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Số Lượng --}}
                    <div class="col-md-4 mb-3">
                        <label for="soluong_nhap" class="form-label fw-bold">Số Lượng Nhập</label>
                        <input type="number" min="1" class="form-control @error('soluong_nhap') is-invalid @enderror" id="soluong_nhap" name="soluong_nhap" value="{{ old('soluong_nhap') }}" required>
                        @error('soluong_nhap') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ngày Nhập --}}
                    <div class="col-md-4 mb-3">
                        <label for="ngaynhap" class="form-label fw-bold">Ngày Nhập Kho</label>
                        <input type="date" class="form-control @error('ngaynhap') is-invalid @enderror" id="ngaynhap" name="ngaynhap" value="{{ old('ngaynhap', date('Y-m-d')) }}" required>
                        @error('ngaynhap') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ngày Hết Hạn --}}
                    <div class="col-md-4 mb-3">
                        <label for="ngayhethan" class="form-label fw-bold">Hạn Sử Dụng (Dự kiến)</label>
                        <input type="date" class="form-control @error('ngayhethan') is-invalid @enderror" id="ngayhethan" name="ngayhethan" value="{{ old('ngayhethan') }}" required>
                        @error('ngayhethan') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.lohang.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-success px-4 shadow-sm">
                        <i class="fas fa-save me-1"></i> Lưu Lô Hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Thêm CSS/JS của Tom Select trực tiếp -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#select-masp", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "-- Gõ mã hoặc tên hoa để tìm --",
            allowEmptyOption: true,
        });
    });
</script>

@endsection