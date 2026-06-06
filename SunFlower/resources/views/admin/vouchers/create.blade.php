@extends('layouts.admin')

@section('title', 'Tạo mã giảm giá')
@section('page_title', 'THÊM MÃ GIẢM GIÁ MỚI')

@section('content')
<style>
    /* CSS XỬ LÝ DARK MODE CHO FORM */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .text-dark { color: #e9ecef !important; }
    [data-bs-theme="dark"] .form-label.text-dark { color: #e9ecef !important; }
    [data-bs-theme="dark"] .bg-light { background-color: #2c3034 !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] input, [data-bs-theme="dark"] select { background-color: #1a1d20; color: white; border-color: #373b3e; }
    [data-bs-theme="dark"] input:focus, [data-bs-theme="dark"] select:focus { background-color: #1a1d20; color: white; }
</style>
<div class="container-fluid px-4">
    <div class="card shadow-sm mb-4 border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-dark">
                <i class="fa-solid fa-plus-circle text-primary me-2"></i>Thiết lập thông tin Voucher
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Mã Voucher <span class="text-danger">*</span></label>
                        <input type="text" name="mavoucher" class="form-control @error('mavoucher') is-invalid @enderror" value="{{ old('mavoucher') }}" placeholder="Ví dụ: VALENTIN2026" required style="text-transform: uppercase;">
                        @error('mavoucher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold text-dark">Tên Chương Trình <span class="text-danger">*</span></label>
                        <input type="text" name="tenvoucher" class="form-control @error('tenvoucher') is-invalid @enderror" value="{{ old('tenvoucher') }}" placeholder="Ví dụ: Giảm giá ngày Lễ Tình Nhân" required>
                        @error('tenvoucher') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Loại Giảm Giá <span class="text-danger">*</span></label>
                        <select name="loai_giam" id="loai_giam" class="form-select">
                            <option value="phan_tram" {{ old('loai_giam') == 'phan_tram' ? 'selected' : '' }}>Giảm theo phần trăm (%)</option>
                            <option value="so_tien" {{ old('loai_giam') == 'so_tien' ? 'selected' : '' }}>Giảm số tiền cụ thể (đ)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Giá Trị Giảm <span class="text-danger">*</span></label>
                        <input type="number" name="gia_tri_giam" class="form-control @error('gia_tri_giam') is-invalid @enderror" value="{{ old('gia_tri_giam') }}" min="0" required>
                        @error('gia_tri_giam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4" id="div_giam_max">
                        <label class="form-label fw-bold text-dark">Số Tiền Giảm Tối Đa (đ)</label>
                        <input type="number" name="giam_max" class="form-control @error('giam_max') is-invalid @enderror" value="{{ old('giam_max') }}" placeholder="Bỏ trống nếu không giới hạn">
                        @error('giam_max') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Giá Trị Đơn Tối Thiểu (đ) <span class="text-danger">*</span></label>
                        <input type="number" name="don_min" class="form-control @error('don_min') is-invalid @enderror" value="{{ old('don_min', 0) }}" min="0" required>
                        @error('don_min') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Số Lượng Phát Hành <span class="text-danger">*</span></label>
                        <input type="number" name="soluong" class="form-control @error('soluong') is-invalid @enderror" value="{{ old('soluong') }}" min="1" required>
                        @error('soluong') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Chế Độ Hiển Thị <span class="text-danger">*</span></label>
                        <select name="hien_thi" class="form-select">
                            <option value="cong_khai" {{ old('hien_thi') == 'cong_khai' ? 'selected' : '' }}>Công khai ở trang đặt hàng</option>
                            <option value="nhap_code" {{ old('hien_thi') == 'nhap_code' ? 'selected' : '' }}>Ẩn (Khách phải tự gõ chữ mã)</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Ngày Bắt Đầu Có Hiệu Lực <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="ngay_bd" class="form-control @error('ngay_bd') is-invalid @enderror" value="{{ old('ngay_bd') }}" required>
                        @error('ngay_bd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Ngày Hết Hạn <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="ngay_kt" class="form-control @error('ngay_kt') is-invalid @enderror" value="{{ old('ngay_kt') }}" required>
                        @error('ngay_kt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark">Trạng Thái Kích Hoạt <span class="text-danger">*</span></label>
                        <select name="trangthai" class="form-select">
                            <option value="1" {{ old('trangthai', 1) == 1 ? 'selected' : '' }}>Bật (Cho phép áp dụng liền)</option>
                            <option value="0" {{ old('trangthai') == 0 ? 'selected' : '' }}>Tắt (Tạm khóa)</option>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark">Phạm Vi Áp Dụng <span class="text-danger">*</span></label>
                        <select name="loai_ap_dung" id="loai_ap_dung" class="form-select mb-3">
                            <option value="tat_ca" {{ old('loai_ap_dung') == 'tat_ca' ? 'selected' : '' }}>Áp dụng cho toàn bộ các sản phẩm</option>
                            <option value="danh_muc" {{ old('loai_ap_dung') == 'danh_muc' ? 'selected' : '' }}>Chỉ áp dụng cho một số danh mục cụ thể</option>
                        </select>
                    </div>

                    <div class="col-md-12 d-none" id="div_danhmuc">
                        <div class="card p-3 border bg-light">
                            <label class="form-label fw-bold text-dark mb-2">Chọn Danh Mục Được Áp Dụng:</label>
                            <div class="row">
                                @foreach($danhmucs as $dm)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input shadow-none" type="checkbox" name="danhmuc_ids[]" value="{{ $dm->madm }}" id="dm_{{ $dm->madm }}" {{ is_array(old('danhmuc_ids')) && in_array($dm->madm, old('danhmuc_ids')) ? 'checked' : '' }}>
                                        <label class="form-check-input-label text-dark" for="dm_{{ $dm->madm }}">
                                            {{ $dm->tendm }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('danhmuc_ids') <small class="text-danger d-block mt-2">{{ $message }}</small> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary px-4 me-2">Quay lại</a>
                    <button type="submit" class="btn btn-primary px-4">Lưu cấu hình</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const loaiGiam = document.getElementById('loai_giam');
        const divGiamMax = document.getElementById('div_giam_max');
        const loaiApDung = document.getElementById('loai_ap_dung');
        const divDanhMuc = document.getElementById('div_danhmuc');

        function handleLoaiGiam() {
            if (loaiGiam.value === 'so_tien') {
                divGiamMax.classList.add('d-none');
            } else {
                divGiamMax.classList.remove('d-none');
            }
        }

        function handleLoaiApDung() {
            if (loaiApDung.value === 'danh_muc') {
                divDanhMuc.classList.remove('d-none');
            } else {
                divDanhMuc.classList.add('d-none');
            }
        }

        loaiGiam.addEventListener('change', handleLoaiGiam);
        loaiApDung.addEventListener('change', handleLoaiApDung);
        
        // Khởi chạy khi load trang để giữ dữ liệu cũ nếu báo lỗi validate
        handleLoaiGiam();
        handleLoaiApDung();
    });
</script>
@endsection