@extends('layouts.admin')

@section('title', 'Nhập Lô Hoa Mới')
@section('page_title', 'NHẬP LÔ HOA MỚI')

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE CHO FORM NHẬP LÔ HÀNG
       ========================================== */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .text-dark, [data-bs-theme="dark"] .form-label { color: #e9ecef !important; }
    [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; border-color: #495057 !important; color: #e9ecef !important; }
    [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select {
        background-color: #2c3034 !important; border-color: #495057 !important; color: #e9ecef !important;
    }
    [data-bs-theme="dark"] .form-control:focus, [data-bs-theme="dark"] .form-select:focus {
        background-color: #2c3034 !important; border-color: var(--sunflower-orange) !important;
        color: #ffffff !important; box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25) !important;
    }
    [data-bs-theme="dark"] .form-control[readonly] { background-color: #1a1d20 !important; color: #adb5bd !important; }
    [data-bs-theme="dark"] .btn-light { background-color: #343a40 !important; color: #dee2e6 !important; border-color: #495057 !important; }
    [data-bs-theme="dark"] .btn-light:hover { background-color: #495057 !important; color: #ffffff !important; }

    /* Preview panel */
    [data-bs-theme="dark"] .product-preview { background-color: #2c3034 !important; border-color: #495057 !important; }
    [data-bs-theme="dark"] .product-preview.active { background-color: #2c3034 !important; border-color: var(--sunflower-orange) !important; }

    /* ==========================================
       CUSTOM STYLES
       ========================================== */
    .product-preview {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        padding: 16px;
        background: #f8f9fa;
        transition: all 0.3s;
        min-height: 100px;
        display: flex;
        align-items: center;
    }
    .product-preview.active { border-color: var(--sunflower-orange); border-style: solid; }
    .product-preview .preview-img {
        width: 70px; height: 70px; border-radius: 8px;
        object-fit: cover; box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .product-preview .preview-placeholder { text-align: center; width: 100%; color: #adb5bd; }
    .product-preview .preview-placeholder i { font-size: 1.5rem; margin-bottom: 4px; opacity: 0.4; }

    .btn-quick-expiry {
        border-radius: 4px; padding: 3px 10px; font-size: 0.78rem; font-weight: 600;
        border: 1px solid #dee2e6; background: #fff; color: #495057; cursor: pointer;
        transition: all 0.15s;
    }
    .btn-quick-expiry:hover, .btn-quick-expiry.active {
        background: var(--sunflower-orange); color: white; border-color: var(--sunflower-orange);
    }
    [data-bs-theme="dark"] .btn-quick-expiry { background: #2c3034; border-color: #495057; color: #adb5bd; }

    .section-label {
        font-size: 0.78rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.5px; color: #adb5bd; margin: 20px 0 12px; padding-bottom: 6px;
        border-bottom: 1px solid #e9ecef;
    }
    [data-bs-theme="dark"] .section-label { border-color: #373b3e; color: #6c757d; }

    /* Modal */
    .confirm-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f3f5; font-size: 0.9rem; }
    .confirm-row:last-child { border-bottom: none; }
    .confirm-row .label { color: #6c757d; }
    .confirm-row .value { font-weight: 700; color: #2d3748; }
    [data-bs-theme="dark"] .confirm-row { border-color: #373b3e; }
    [data-bs-theme="dark"] .confirm-row .value { color: #e9ecef; }
    .confirm-total {
        background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 8px;
        padding: 10px 16px; display: flex; justify-content: space-between; align-items: center; margin-top: 10px;
    }
    .confirm-total .total-value { font-weight: 800; font-size: 1.15rem; color: var(--sunflower-orange); }
    [data-bs-theme="dark"] .confirm-total { background: #2c3034; border-color: #373b3e; }
</style>

<div class="container-fluid mt-3 pb-5">
    <div class="mb-3">
        <a href="{{ route('admin.lohang.index') }}" class="text-decoration-none text-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i> Vui lòng kiểm tra lại dữ liệu nhập vào!
        </div>
    @endif

    <form action="{{ route('admin.lohang.store') }}" method="POST" id="formNhapLo">
        @csrf

        {{-- ====== CARD 1: CHỌN SẢN PHẨM ====== --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                    <i class="fa-solid fa-seedling me-2"></i> Bước 1 — Chọn sản phẩm nhập kho
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="masp" class="form-label fw-bold">Sản phẩm (Hoa) <span class="text-danger">*</span></label>
                        <select class="form-control tom-select @error('masp') is-invalid @enderror" id="select-masp" name="masp" required>
                            <option value=""></option>
                            @foreach($sanPhams as $sp)
                                <option value="{{ $sp->masp }}" {{ old('masp') == $sp->masp ? 'selected' : '' }}>
                                    [{{ $sp->masp }}] - {{ $sp->tensp }}
                                </option>
                            @endforeach
                        </select>
                        @error('masp') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Gõ mã hoặc tên hoa để tìm nhanh</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Xem trước sản phẩm</label>
                        <div class="product-preview" id="product-preview">
                            <div class="preview-placeholder" id="preview-placeholder">
                                <i class="fa-solid fa-image d-block"></i>
                                <span>Chọn sản phẩm để xem thông tin</span>
                            </div>
                            <div class="d-none" id="preview-content">
                                <div class="d-flex align-items-center gap-3 w-100">
                                    <img src="" class="preview-img" id="preview-img" alt="">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1" id="preview-name">—</h6>
                                        <div style="font-size: 0.85rem;">
                                            <div><i class="fa-solid fa-tag text-muted me-1"></i> Giá bán: <strong id="preview-price">—</strong></div>
                                            <div><i class="fa-solid fa-layer-group text-muted me-1"></i> Danh mục: <span id="preview-category">—</span></div>
                                            <div><i class="fa-solid fa-boxes-stacked text-muted me-1"></i> Tồn kho: <strong id="preview-stock" style="color: var(--sunflower-orange);">—</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== CARD 2: THÔNG TIN NHẬP KHO ====== --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);">
                    <i class="fa-solid fa-clipboard-list me-2"></i> Bước 2 — Thông tin nhập kho
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    {{-- Mã Lô --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Mã Lô Hàng</label>
                        <input type="text" class="form-control bg-light text-muted fw-bold" value="(Mã sinh tự động)" readonly>
                    </div>
                    {{-- Số Lượng --}}
                    <div class="col-md-4 mb-3">
                        <label for="soluong_nhap" class="form-label fw-bold">Số Lượng Nhập <span class="text-danger">*</span></label>
                        <input type="number" min="1" class="form-control @error('soluong_nhap') is-invalid @enderror" id="soluong_nhap" name="soluong_nhap" value="{{ old('soluong_nhap') }}" placeholder="Nhập số lượng..." required>
                        @error('soluong_nhap') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    {{-- Giá Nhập --}}
                    <div class="col-md-4 mb-3">
                        <label for="gia_nhap" class="form-label fw-bold">Giá Nhập (đ/đơn vị)</label>
                        <input type="number" min="0" class="form-control @error('gia_nhap') is-invalid @enderror" id="gia_nhap" name="gia_nhap" value="{{ old('gia_nhap') }}" placeholder="VD: 50000">
                        @error('gia_nhap') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="section-label"><i class="fa-solid fa-calendar me-1"></i> Thời hạn</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ngaynhap" class="form-label fw-bold">Ngày Nhập Kho <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('ngaynhap') is-invalid @enderror" id="ngaynhap" name="ngaynhap" value="{{ old('ngaynhap', date('Y-m-d')) }}" required>
                        @error('ngaynhap') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ngayhethan" class="form-label fw-bold">Hạn Sử Dụng (Dự kiến) <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('ngayhethan') is-invalid @enderror" id="ngayhethan" name="ngayhethan" value="{{ old('ngayhethan') }}" required>
                        @error('ngayhethan') <span class="text-danger small">{{ $message }}</span> @enderror
                        <div class="d-flex gap-2 mt-2 flex-wrap align-items-center">
                            <small class="text-muted">Nhanh:</small>
                            <button type="button" class="btn-quick-expiry" data-days="3">+3 ngày</button>
                            <button type="button" class="btn-quick-expiry" data-days="5">+5 ngày</button>
                            <button type="button" class="btn-quick-expiry" data-days="7">+7 ngày</button>
                            <button type="button" class="btn-quick-expiry" data-days="14">+14 ngày</button>
                        </div>
                    </div>
                </div>

                <div class="section-label"><i class="fa-solid fa-circle-info me-1"></i> Thông tin bổ sung</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nhacungcap" class="form-label fw-bold">Nhà Cung Cấp</label>
                        <select class="form-control @error('nhacungcap') is-invalid @enderror" id="select-nhacungcap" name="nhacungcap">
                            <option value=""></option>
                            @foreach($nhaCungCaps as $ncc)
                                <option value="{{ $ncc->ten_ncc }}" {{ old('nhacungcap') == $ncc->ten_ncc ? 'selected' : '' }}>{{ $ncc->ten_ncc }}</option>
                            @endforeach
                        </select>
                        @error('nhacungcap') <span class="text-danger small">{{ $message }}</span> @enderror
                        <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i>Chọn từ danh sách hoặc gõ tên mới</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ghichu" class="form-label fw-bold">Ghi Chú / Tình Trạng Hoa</label>
                        <textarea class="form-control @error('ghichu') is-invalid @enderror" id="ghichu" name="ghichu" rows="3" placeholder="VD: Hoa tươi, màu sắc đẹp...">{{ old('ghichu') }}</textarea>
                        @error('ghichu') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('admin.lohang.index') }}" class="btn btn-light px-4">
                        <i class="fa-solid fa-xmark me-1"></i> Hủy bỏ
                    </a>
                    <button type="button" class="btn text-white px-4 shadow-sm" style="background-color: var(--sunflower-orange);" id="btn-confirm">
                        <i class="fas fa-check-circle me-1"></i> Xác nhận & Lưu
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- ====== MODAL XÁC NHẬN ====== --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: var(--sunflower-orange);">
                <h5 class="modal-title fw-bold" id="confirmModalLabel">
                    <i class="fa-solid fa-clipboard-check me-2"></i>Xác nhận nhập lô hoa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="confirm-details">
                    <div class="confirm-row"><span class="label">Sản phẩm</span><span class="value" id="cf-product">—</span></div>
                    <div class="confirm-row"><span class="label">Số lượng nhập</span><span class="value" id="cf-quantity">—</span></div>
                    <div class="confirm-row"><span class="label">Giá nhập / đơn vị</span><span class="value" id="cf-price">—</span></div>
                    <div class="confirm-row"><span class="label">Nhà cung cấp</span><span class="value" id="cf-supplier">—</span></div>
                    <div class="confirm-row"><span class="label">Ngày nhập</span><span class="value" id="cf-date-in">—</span></div>
                    <div class="confirm-row"><span class="label">Hạn sử dụng</span><span class="value" id="cf-date-exp">—</span></div>
                    <div class="confirm-row"><span class="label">Ghi chú</span><span class="value" id="cf-note" style="max-width: 200px; text-align: right;">—</span></div>
                </div>
                <div class="confirm-total" id="confirm-total" style="display: none;">
                    <span class="fw-bold text-muted"><i class="fa-solid fa-calculator me-2"></i>Tổng tiền nhập</span>
                    <span class="total-value" id="cf-total">0đ</span>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Quay lại</button>
                <button type="button" class="btn btn-success px-4 fw-bold" id="btn-submit-final">
                    <i class="fa-solid fa-paper-plane me-1"></i> Xác nhận Lưu
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tom Select CDN -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const tomSelectInstance = new TomSelect("#select-masp", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "-- Gõ mã hoặc tên hoa để tìm --",
        allowEmptyOption: true,
        onChange: function(value) { loadProductPreview(value); }
    });

    // Tom Select cho Nhà Cung Cấp (cho phép gõ mới + xóa lựa chọn)
    new TomSelect("#select-nhacungcap", {
        create: true,
        plugins: ['clear_button'],
        sortField: { field: "text", direction: "asc" },
        placeholder: "-- Chọn hoặc gõ tên NCC mới --",
        allowEmptyOption: true,
        createFilter: function(input) { return input.length >= 2; },
        render: {
            option_create: function(data, escape) {
                return '<div class="create"><i class="fa-solid fa-plus me-1"></i> Thêm mới: <strong>' + escape(data.input) + '</strong></div>';
            }
        }
    });

    function loadProductPreview(masp) {
        const panel = document.getElementById('product-preview');
        const placeholder = document.getElementById('preview-placeholder');
        const content = document.getElementById('preview-content');
        if (!masp) { panel.classList.remove('active'); placeholder.classList.remove('d-none'); content.classList.add('d-none'); return; }

        fetch(`{{ url('admin/lohang/product-info') }}/${masp}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) return;
            document.getElementById('preview-img').src = data.hinhanh || '{{ asset("images/bg-sunflower.jpg") }}';
            document.getElementById('preview-name').textContent = data.tensp;
            document.getElementById('preview-price').textContent = Number(data.giakm || data.giaban).toLocaleString('vi-VN') + 'đ';
            document.getElementById('preview-category').textContent = data.danhmuc;
            document.getElementById('preview-stock').textContent = Number(data.tong_ton).toLocaleString('vi-VN') + ' bông';
            panel.classList.add('active'); placeholder.classList.add('d-none'); content.classList.remove('d-none');
        })
        .catch(err => console.error('Lỗi tải thông tin SP:', err));
    }

    const oldMasp = tomSelectInstance.getValue();
    if (oldMasp) loadProductPreview(oldMasp);

    // Nút nhanh HSD
    document.querySelectorAll('.btn-quick-expiry').forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            const ngayNhap = document.getElementById('ngaynhap').value;
            if (!ngayNhap) { alert('Vui lòng chọn ngày nhập kho trước!'); return; }
            const date = new Date(ngayNhap);
            date.setDate(date.getDate() + days);
            document.getElementById('ngayhethan').value = `${date.getFullYear()}-${String(date.getMonth()+1).padStart(2,'0')}-${String(date.getDate()).padStart(2,'0')}`;
            document.querySelectorAll('.btn-quick-expiry').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Modal xác nhận
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('btn-confirm').addEventListener('click', function() {
        const form = document.getElementById('formNhapLo');
        const masp = tomSelectInstance.getValue();
        const soluong = document.getElementById('soluong_nhap').value;
        const ngaynhap = document.getElementById('ngaynhap').value;
        const ngayhethan = document.getElementById('ngayhethan').value;
        if (!masp || !soluong || !ngaynhap || !ngayhethan) { form.reportValidity(); return; }

        const spName = document.getElementById('preview-name')?.textContent || 'N/A';
        const gia = Number(document.getElementById('gia_nhap').value || 0);
        const sl = Number(soluong);
        function fmtDate(d) { if(!d) return '—'; const p = d.split('-'); return `${p[2]}/${p[1]}/${p[0]}`; }

        document.getElementById('cf-product').textContent = spName;
        document.getElementById('cf-quantity').textContent = sl.toLocaleString('vi-VN') + ' bông';
        document.getElementById('cf-price').textContent = gia ? gia.toLocaleString('vi-VN') + 'đ' : 'Không nhập';
        document.getElementById('cf-supplier').textContent = document.getElementById('select-nhacungcap').tomselect.getValue() || 'Không nhập';
        document.getElementById('cf-date-in').textContent = fmtDate(ngaynhap);
        document.getElementById('cf-date-exp').textContent = fmtDate(ngayhethan);
        document.getElementById('cf-note').textContent = document.getElementById('ghichu').value || 'Không có';

        const totalDiv = document.getElementById('confirm-total');
        if (gia > 0) { totalDiv.style.display = 'flex'; document.getElementById('cf-total').textContent = (gia * sl).toLocaleString('vi-VN') + 'đ'; }
        else { totalDiv.style.display = 'none'; }
        confirmModal.show();
    });

    document.getElementById('btn-submit-final').addEventListener('click', function() {
        document.getElementById('formNhapLo').submit();
    });
});
</script>
@endsection