@extends('layouts.admin')

@section('title', 'Thêm danh mục mới')
@section('page_title', 'THÊM DANH MỤC MỚI')

@section('content')
<div class="container-fluid mt-3 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-3">
                <a href="{{ route('admin.categories.index') }}" class="text-decoration-none text-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                        <i class="fa-solid fa-plus-circle me-2"></i> Thông tin danh mục
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-4">
                                    <label for="madm" class="form-label fw-bold">Mã danh mục <span class="text-danger">*</span></label>
                                    <input type="text" name="madm" id="madm" class="form-control @error('madm') is-invalid @enderror" 
                                           placeholder="Ví dụ: DM001" value="{{ old('madm') }}">
                                    @error('madm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="tendm" class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" name="tendm" id="tendm" class="form-control @error('tendm') is-invalid @enderror" 
                                           placeholder="Nhập tên loại hoa (vợ, khai trương...)" value="{{ old('tendm') }}">
                                    @error('tendm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold d-block">Hình ảnh đại diện</label>
                                <div class="border rounded p-2 bg-light mb-2 text-center" style="min-height: 150px; display: flex; align-items: center; justify-content: center;">
                                    <img id="img-preview" src="{{ asset('images/bg-sunflower.jpg') }}" 
                                         class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                                </div>
                                <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(event)">
                                @error('hinhanh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light px-4">Làm lại</button>
                            <button type="submit" class="btn text-white px-5 shadow-sm" style="background-color: var(--sunflower-orange);">
                                <i class="fa-solid fa-save me-2"></i> Lưu danh mục
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function(){
            const output = document.getElementById('img-preview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection