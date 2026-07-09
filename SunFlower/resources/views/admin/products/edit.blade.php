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
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="mota_chitiet" class="form-label fw-bold mb-0">Mô tả chi tiết</label>
                                        <button type="button" id="btn-ai-generate" class="btn btn-sm text-white" style="background: linear-gradient(45deg, #8A2387, #E94057, #F27121); border: none; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                            <i class="fa-solid fa-wand-magic-sparkles"></i> ✨ Viết bằng AI
                                        </button>
                                    </div>
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
                                        @php
                                            $editProdImg = asset('images/bg-sunflower.jpg');
                                            if(!empty($product->hinhanh)){
                                                $editProdImg = str_starts_with($product->hinhanh, 'http') ? $product->hinhanh : asset('storage/' . ltrim($product->hinhanh, '/'));
                                            }
                                        @endphp
                                        <img id="img-preview" src="{{ $editProdImg }}" 
                                            class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                                    </div>
                                    
                                    <label for="hinhanh" class="form-label fw-bold">Thay đổi ảnh mới</label>
                                    <input type="file" name="hinhanh" id="hinhanh" class="form-control @error('hinhanh') is-invalid @enderror" 
                                           accept="image/*" onchange="previewImage(event)">
                                    @error('hinhanh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ẢNH PHỤ HIỆN CÓ --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold d-block">
                                        Ảnh phụ (Gallery) 
                                        <span class="text-muted fw-normal">— {{ $product->hinhAnhPhu->count() }}/5 ảnh</span>
                                    </label>
                                    
                                    @if($product->hinhAnhPhu->count() > 0)
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @foreach($product->hinhAnhPhu as $anhPhu)
                                                <div class="position-relative" style="width: 90px;">
                                                    <img src="{{ $anhPhu->duong_dan }}" class="rounded border" 
                                                         style="width:90px; height:90px; object-fit:cover;">
                                                    <div class="form-check position-absolute bottom-0 start-0 w-100 text-center" style="background: rgba(0,0,0,0.6); border-radius: 0 0 4px 4px;">
                                                        <label class="form-check-label text-white" style="font-size: 10px; cursor: pointer;">
                                                            <input type="checkbox" name="xoa_anh_phu[]" value="{{ $anhPhu->id }}" class="form-check-input" style="width:12px; height:12px;">
                                                            Xóa
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <small class="text-danger d-block mb-2"><i class="fa-solid fa-info-circle"></i> Tick vào ảnh muốn xóa, rồi nhấn "Cập nhật".</small>
                                    @endif

                                    @if($product->hinhAnhPhu->count() < 5)
                                        <label class="form-label fw-bold">Thêm ảnh phụ mới</label>
                                        <input type="file" name="hinhanh_phu[]" id="hinhanh_phu" class="form-control @error('hinhanh_phu.*') is-invalid @enderror" 
                                               accept="image/*" multiple onchange="previewGallery(event)">
                                        @error('hinhanh_phu.*')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted mt-1 d-block">Còn có thể thêm {{ 5 - $product->hinhAnhPhu->count() }} ảnh nữa.</small>
                                        <div id="gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                                    @endif
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

    // Xử lý sự kiện click nút AI
    document.getElementById('btn-ai-generate').addEventListener('click', function() {
        const tensp = document.getElementById('tensp').value.trim();
        if (!tensp) {
            alert('Vui lòng nhập "Tên sản phẩm" trước khi nhờ AI viết mô tả!');
            document.getElementById('tensp').focus();
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        
        // Hiển thị trạng thái loading
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang nhờ AI viết... ⏳';
        btn.disabled = true;

        // Gửi AJAX request
        fetch('{{ route('admin.products.generate-desc') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ tensp: tensp })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Đẩy nội dung vào CKEditor
                CKEDITOR.instances.mota_chitiet.setData(data.description);
                alert('✨ AI đã viết xong mô tả! Bạn có thể xem và chỉnh sửa lại theo ý muốn.');
            } else {
                alert('Lỗi: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi kết nối khi gọi AI!');
        })
        .finally(() => {
            // Khôi phục nút
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    });

    // Preview ảnh phụ mới
    function previewGallery(event) {
        const container = document.getElementById('gallery-preview');
        if (!container) return;
        container.innerHTML = '';
        const files = event.target.files;
        const maxFiles = Math.min(files.length, 5);
        
        for (let i = 0; i < maxFiles; i++) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative; width:80px; height:80px;';
                div.innerHTML = `<img src="${e.target.result}" class="rounded border" style="width:80px; height:80px; object-fit:cover;">
                                 <span class="position-absolute top-0 end-0 badge bg-success" style="font-size:10px;">Mới</span>`;
                container.appendChild(div);
            };
            reader.readAsDataURL(files[i]);
        }
    }
</script>
@endsection