@extends('layouts.admin')

@section('title', 'Lập Phiếu Hủy Hàng')

@section('content')
<div class="container-fluid">
    {{-- Nút Quay lại mẫu thanh mảnh --}}
    <div class="mb-3">
        <a href="{{ route('admin.phieuhuyhang.index') }}" class="text-secondary text-decoration-none shadow-none">
            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>

    <h1 class="h3 mb-4 text-gray-800">Lập Phiếu Hủy Hàng</h1>

    @if($errors->any())
        <div class="alert alert-danger shadow-sm">
            <i class="fas fa-exclamation-triangle me-2"></i> Vui lòng kiểm tra lại dữ liệu nhập vào!
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger shadow-sm">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4 border-0">
        <div class="card-body">
            <form action="{{ route('admin.phieuhuyhang.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- Mã Phiếu Hủy --}}
                    <div class="col-md-4 mb-3">
                        <label for="maphieu" class="form-label fw-bold">Mã Phiếu Hủy</label>
                        <input type="text" class="form-control bg-light" id="maphieu" name="maphieu" value="{{ $newMaPhieu }}" readonly>
                    </div>

                    {{-- Chọn Lô Hàng (Sử dụng Tom Select) --}}
                    <div class="col-md-8 mb-3">
                        <label for="malo" class="form-label fw-bold">Chọn Lô Hàng Cần Hủy</label>
                        <select class="form-control tom-select @error('malo') is-invalid @enderror" id="select-malo" name="malo" required>
                            <option value="">-- Gõ mã lô hoặc tên hoa để tìm --</option>
                            @foreach($loHangs as $lo)
                                {{-- Lưu trữ tồn kho vào thuộc tính data-ton để dùng JS xử lý --}}
                                <option value="{{ $lo->malo }}" data-ton="{{ $lo->soluong_ton }}" {{ old('malo') == $lo->malo ? 'selected' : '' }}>
                                    [{{ $lo->malo }}] - {{ $lo->sanpham->tensp ?? 'N/A' }} - (Tồn kho hiện tại: {{ $lo->soluong_ton }})
                                </option>
                            @endforeach
                        </select>
                        @error('malo') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Số Lượng Hủy --}}
                    <div class="col-md-6 mb-3">
                        <label for="soluong_huy" class="form-label fw-bold">Số Lượng Hủy</label>
                        <input type="number" min="1" class="form-control @error('soluong_huy') is-invalid @enderror" id="soluong_huy" name="soluong_huy" value="{{ old('soluong_huy') }}" required placeholder="Nhập số lượng hoa bị hỏng...">
                        <small id="tonkho-hint" class="text-muted fst-italic">Vui lòng chọn lô hàng để xem số lượng có thể hủy.</small>
                        @error('soluong_huy') <span class="text-danger small d-block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Ngày Hủy --}}
                    <div class="col-md-6 mb-3">
                        <label for="ngayhuy" class="form-label fw-bold">Ngày Hủy</label>
                        <input type="date" class="form-control @error('ngayhuy') is-invalid @enderror" id="ngayhuy" name="ngayhuy" value="{{ old('ngayhuy', date('Y-m-d')) }}" required>
                        @error('ngayhuy') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    {{-- Lý do hủy --}}
                    <div class="col-md-12 mb-3">
                        <label for="lydo" class="form-label fw-bold">Lý Do Hủy</label>
                        <textarea class="form-control @error('lydo') is-invalid @enderror" id="lydo" name="lydo" rows="3" required placeholder="Ví dụ: Hoa dập nát do vận chuyển, hoa héo do hết hạn...">{{ old('lydo') }}</textarea>
                        @error('lydo') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.phieuhuyhang.index') }}" class="btn btn-light px-4">Hủy bỏ</a>
                    <button type="submit" class="btn btn-danger px-4 shadow-sm">
                        <i class="fas fa-trash-alt me-1"></i> Xác Nhận Hủy Hàng
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Thêm thư viện Tom Select --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Khởi tạo Tom Select
        let selectMalo = new TomSelect("#select-malo", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "-- Gõ mã lô hoặc tên hoa để tìm --",
            allowEmptyOption: true,
        });

        // 2. Logic cập nhật số lượng hủy tối đa
        let inputSoLuong = document.getElementById('soluong_huy');
        let hintTonKho = document.getElementById('tonkho-hint');
        let selectElement = document.getElementById('select-malo');

        // Khi người dùng chọn một lô hàng khác
        selectMalo.on('change', function(value) {
            if(value) {
                // Lấy option đang được chọn
                let selectedOption = selectElement.options[selectElement.selectedIndex];
                let tonKho = selectedOption.getAttribute('data-ton');
                
                // Cập nhật thuộc tính max cho input số lượng
                inputSoLuong.setAttribute('max', tonKho);
                
                // Hiển thị gợi ý cho nhân viên
                hintTonKho.innerHTML = `<span class="text-primary fw-bold">Lô này có thể hủy tối đa: ${tonKho} sản phẩm.</span>`;
                hintTonKho.classList.remove('text-muted');
                
                // Nếu số lượng nhập đang lớn hơn tồn kho thì reset về tồn kho
                if(parseInt(inputSoLuong.value) > parseInt(tonKho)) {
                    inputSoLuong.value = tonKho;
                }
            } else {
                inputSoLuong.removeAttribute('max');
                hintTonKho.innerHTML = 'Vui lòng chọn lô hàng để xem số lượng có thể hủy.';
                hintTonKho.classList.add('text-muted');
            }
        });
    });
</script>

@endsection