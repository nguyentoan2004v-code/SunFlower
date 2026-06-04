@extends('layouts.admin')

@section('title', 'Sửa Khách hàng')
@section('page_title', 'CẬP NHẬT THÔNG TIN KHÁCH HÀNG')

@section('content')
<style>
    /* DARK MODE CHO FORM NHẬP LIỆU */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .form-control { 
        background-color: #1a1d20 !important; 
        border-color: #373b3e !important; 
        color: #e9ecef !important; 
    }
    [data-bs-theme="dark"] .form-label { color: #e9ecef !important; }
</style>

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                        <i class="fas fa-user-edit me-2"></i> Chỉnh sửa: {{ $khachhang->makh }}
                    </h5>
                    <a href="{{ route('admin.khachhang.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger shadow-sm rounded-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.khachhang.update', $khachhang->makh) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên <span class="text-danger">*</span></label>
                                <input type="text" name="hoten" class="form-control" value="{{ old('hoten', $khachhang->hoten) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="sdt" class="form-control" value="{{ old('sdt', $khachhang->sdt) }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" name="ngaysinh" class="form-control" value="{{ old('ngaysinh', $khachhang->ngaysinh) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Địa chỉ</label>
                                <input type="text" name="diachi" class="form-control" value="{{ old('diachi', $khachhang->diachi) }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn text-white px-4 py-2" style="background-color: var(--sunflower-orange);">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection