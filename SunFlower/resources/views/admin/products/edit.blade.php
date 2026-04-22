@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')
@section('page_title', 'CHỈNH SỬA SẢN PHẨM')

@section('content')
<div class="container-fluid mt-3 pb-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="mb-3">
                <a href="{{ route('admin.products.index') }}" class="text-decoration-none text-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Cập nhật thông tin: <span class="text-dark">{{ $product->tensp }}</span>
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.update', $product->masp) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label fw-bold">Mã sản phẩm</label>
                                        <input type="text" class="form-control bg-light" value="{{ $product->masp }}" readonly>
                                        <small class="text-muted italic">Mã sản phẩm không được phép thay đổi.</small>
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label for="tensp" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                        <input type="text" name="tensp" id="tensp" class="form-control @error('tensp') is-invalid @enderror" 
                                               value="{{ old('tensp', $product->tensp) }}">
                                        @error('tensp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="madm" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                        <select name="madm" id="madm" class="form-select @error('madm') is-invalid @enderror">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->madm }}" {{ old('madm', $product->madm) == $cat->madm ? 'selected' : '' }}>
                                                    {{ $cat->tendm }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('madm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="giaban" class="form-label fw-bold">Giá bán (₫) <span class="text-danger">*</span></label>
                                        <input type="number" name="giaban" id="giaban" class="form-control @error('giaban') is-invalid @enderror" 
                                               value="{{ old('giaban', $product->giaban) }}">
                                        @error('giaban')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="giakm" class="form-label fw-bold">Giá KM (₫)</label>
                                        <input type="number" name="giakm" id="giakm" class="form-control @error('giakm') is-invalid @enderror" 
                                               value="{{ old('giakm', $product->giakm) }}">
                                        @error('giakm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="mota" class="form-label fw-bold">Mô tả sản phẩm</label>
                                    <textarea name="mota" id="mota" rows="5" class="form-control @error('mota') is-invalid @enderror">{{ old('mota', $product->mota) }}</textarea>
                                    @error('mota')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold d-block">Hình ảnh hiện tại</label>
                                    <div class="border rounded p-2 bg-light mb-3 text-center">
                                        <img id="img-preview" src="{{ route('product.image', $product->masp) }}" 
                                             class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                                    </div>
                                    
                                    <label for="hinhanh" class="form-label fw-bold">Thay đổi ảnh mới</label>
                                    <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" 
                                           accept="image/*" onchange="previewImage(event)">
                                    @error('hinhanh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
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