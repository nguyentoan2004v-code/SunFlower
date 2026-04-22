@extends('layouts.admin')

@section('title', 'Thêm sản phẩm mới')
@section('page_title', 'THÊM SẢN PHẨM MỚI')

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
                    <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                        <i class="fa-solid fa-plus-circle me-2"></i> Nhập thông tin sản phẩm
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="masp" class="form-label fw-bold">Mã sản phẩm <span class="text-danger">*</span></label>
                                        <input type="text" name="masp" id="masp" class="form-control @error('masp') is-invalid @enderror" 
                                               placeholder="Ví dụ: SP001" value="{{ old('masp') }}">
                                        @error('masp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-8 mb-3">
                                        <label for="tensp" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                        <input type="text" name="tensp" id="tensp" class="form-control @error('tensp') is-invalid @enderror" 
                                               placeholder="Nhập tên hoa hoặc quà tặng" value="{{ old('tensp') }}">
                                        @error('tensp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="madm" class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                                        <select name="madm" id="madm" class="form-select @error('madm') is-invalid @enderror">
                                            <option value="">-- Chọn danh mục --</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->madm }}" {{ old('madm') == $cat->madm ? 'selected' : '' }}>
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
                                               placeholder="0" value="{{ old('giaban') }}">
                                        @error('giaban')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="giakm" class="form-label fw-bold">Giá KM (nếu có)</label>
                                        <input type="number" name="giakm" id="giakm" class="form-control @error('giakm') is-invalid @enderror" 
                                               placeholder="0" value="{{ old('giakm') }}">
                                        @error('giakm')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="mota" class="form-label fw-bold">Mô tả sản phẩm</label>
                                    <textarea name="mota" id="mota" rows="5" class="form-control @error('mota') is-invalid @enderror" 
                                              placeholder="Nhập chi tiết về sản phẩm...">{{ old('mota') }}</textarea>
                                    @error('mota')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3 text-center">
                                    <label class="form-label fw-bold d-block text-start">Hình ảnh sản phẩm</label>
                                    <div class="border rounded p-2 bg-light mb-2" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <img id="img-preview" src="{{ asset('images/bg-sunflower.jpg') }}" 
                                             class="img-fluid rounded shadow-sm" style="max-height: 250px; display: block;">
                                    </div>
                                    <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" 
                                           accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted mt-1 d-block text-start italic">Định dạng: JPG, PNG, JPEG. Tối đa 2MB.</small>
                                    @error('hinhanh')
                                        <div class="invalid-feedback text-start">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light px-4">Làm lại</button>
                            <button type="submit" class="btn text-white px-5 shadow-sm" style="background-color: var(--sunflower-orange);">
                                <i class="fa-solid fa-save me-2"></i> Lưu sản phẩm
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