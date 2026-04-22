@extends('layouts.admin')

@section('title', 'Cập nhật danh mục')
@section('page_title', 'CHỈNH SỬA DANH MỤC')

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
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Cập nhật: {{ $category->tendm }}
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.categories.update', $category->madm) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-7">
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Mã danh mục</label>
                                    <input type="text" class="form-control bg-light" value="{{ $category->madm }}" readonly>
                                    <small class="text-muted">Mã định danh không thể thay đổi.</small>
                                </div>

                                <div class="mb-4">
                                    <label for="tendm" class="form-label fw-bold">Tên danh mục <span class="text-danger">*</span></label>
                                    <input type="text" name="tendm" id="tendm" class="form-control @error('tendm') is-invalid @enderror" 
                                           value="{{ old('tendm', $category->tendm) }}">
                                    @error('tendm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold d-block">Hình ảnh hiện tại</label>
                                <div class="border rounded p-2 bg-light mb-3 text-center">
                                    <img id="img-preview" 
                                         src="{{ $category->hinhanh ? asset('storage/image/' . $category->hinhanh) : asset('images/bg-sunflower.jpg') }}" 
                                         class="img-fluid rounded shadow-sm" style="max-height: 150px;">
                                </div>
                                
                                <label for="hinhanh" class="form-label fw-bold">Thay ảnh mới</label>
                                <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" 
                                       accept="image/*" onchange="previewImage(event)">
                                @error('hinhanh')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                            <button type="submit" class="btn text-white px-5 shadow-sm" style="background-color: var(--sunflower-orange);">
                                <i class="fa-solid fa-save me-2"></i> Cập nhật ngay
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