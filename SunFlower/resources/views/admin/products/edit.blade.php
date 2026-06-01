@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')
@section('page_title', 'CHỈNH SỬA SẢN PHẨM')

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE (Không ảnh hưởng Light Mode)
       ========================================== */
    
    /* 1. Nền Card và Header */
    [data-bs-theme="dark"] .card {
        background-color: #212529 !important;
        border: 1px solid #373b3e !important;
    }
    [data-bs-theme="dark"] .card-header.bg-white {
        background-color: #2c3034 !important;
        border-bottom: 1px solid #373b3e !important;
    }

    /* 2. Màu chữ tiêu đề và Label */
    [data-bs-theme="dark"] .text-dark,
    [data-bs-theme="dark"] .text-primary,
    [data-bs-theme="dark"] .form-label {
        color: #e9ecef !important;
    }
    
    /* 3. Khung Preview Hình ảnh (bỏ nền sáng) */
    [data-bs-theme="dark"] .bg-light {
        background-color: #2c3034 !important;
        border-color: #495057 !important;
        color: #e9ecef !important;
    }
    
    /* 4. Các thẻ Input / Textarea / Select */
    [data-bs-theme="dark"] .form-control,
    [data-bs-theme="dark"] .form-select {
        background-color: #2c3034 !important;
        border-color: #495057 !important;
        color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .form-control:focus,
    [data-bs-theme="dark"] .form-select:focus {
        background-color: #2c3034 !important;
        border-color: var(--sunflower-orange) !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25) !important;
    }
    [data-bs-theme="dark"] .form-control[readonly] {
        background-color: #1a1d20 !important;
        color: #adb5bd !important;
    }

    /* 5. Nút bấm (Buttons) */
    [data-bs-theme="dark"] .btn-light {
        background-color: #343a40 !important;
        color: #dee2e6 !important;
        border-color: #495057 !important;
    }
    [data-bs-theme="dark"] .btn-light:hover {
        background-color: #495057 !important;
        color: #ffffff !important;
    }

    /* 6. CKEditor Dark Mode cơ bản (Ép màu viền) */
    [data-bs-theme="dark"] .cke_chrome {
        border-color: #495057 !important;
    }
</style>
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
                                <div class="mb-3">
                                    <label for="mota_chitiet" class="form-label fw-bold">Mô tả chi tiết </label>
                                    <textarea name="mota_chitiet" id="mota_chitiet" rows="8" class="form-control @error('mota_chitiet') is-invalid @enderror" 
                                              placeholder="Nhập đầy đủ chi tiết, ý nghĩa sản phẩm, hướng dẫn chăm sóc...">{{ old('mota_chitiet', $product->mota_chitiet) }}</textarea>
                                    @error('mota_chitiet')
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

<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    // Kiểm tra xem web đang ở chế độ Dark Mode hay không
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';

    CKEDITOR.replace('mota_chitiet', {
        height: 400,
        versionCheck: false, // Tắt ngay cái bảng cảnh báo đỏ đỏ chướng mắt
        on: {
            instanceReady: function(evt) {
                // Bơm trực tiếp CSS vào lõi iframe của CKEditor khi nó vừa load xong
                if (isDark) {
                    evt.editor.document.appendStyleText(
                        'body { background-color: #2c3034 !important; color: #ffffff !important; }' +
                        'p, span, li, h1, h2, h3 { color: #ffffff !important; }'
                    );
                }
            }
        }
    });

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