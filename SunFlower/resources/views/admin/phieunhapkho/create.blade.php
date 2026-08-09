@extends('layouts.admin')

@section('title', 'Tạo Phiếu Nhập Kho')
@section('page_title', 'TẠO PHIẾU NHẬP KHO NGUYÊN LIỆU')

@section('content')
<div class="container-fluid mt-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.phieunhapkho.store') }}" method="POST">
        @csrf
        <div class="row">
            {{-- THÔNG TIN CHUNG (HEADER) --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-info-circle me-2"></i> Thông tin chung</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select name="id_nhacungcap" class="form-select" required>
                                <option value="">-- Chọn Nhà Cung Cấp --</option>
                                @foreach($nhaCungCaps as $ncc)
                                    <option value="{{ $ncc->id }}" {{ old('id_nhacungcap') == $ncc->id ? 'selected' : '' }}>{{ $ncc->ten_ncc }}</option>
                                @endforeach
                            </select>
                            @error('id_nhacungcap') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ghi chú</label>
                            <textarea name="ghichu" class="form-control" rows="3" placeholder="VD: Giao hàng trễ, hoa ướt...">{{ old('ghichu') }}</textarea>
                        </div>
                        
                        <div class="alert alert-info py-2 small">
                            <i class="fa-solid fa-user-pen me-1"></i> Người lập: <strong>{{ Auth::guard('nhanvien')->user()->hoten ?? Auth::guard('nhanvien')->user()->manv }}</strong><br>
                            <i class="fa-solid fa-clock me-1"></i> Ngày lập: <strong>{{ date('d/m/Y') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CHI TIẾT NHẬP (LINE ITEMS) --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-list-check me-2"></i> Danh sách Nguyên liệu nhập</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMaterialRow()">
                            <i class="fa-solid fa-plus me-1"></i> Thêm mặt hàng
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="importTable">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 30%">Nguyên liệu</th>
                                        <th style="width: 15%">Số lượng</th>
                                        <th style="width: 20%">Đơn giá nhập (đ)</th>
                                        <th style="width: 20%">Thành tiền (đ)</th>
                                        <th style="width: 15%">HSD (Dự kiến)</th>
                                        <th style="width: 5%">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="importBody">
                                    {{-- JS sẽ render các dòng vào đây --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">TỔNG CỘNG:</td>
                                        <td class="text-end fw-bold text-danger fs-5" id="grandTotal">0</td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-end py-3">
                        <a href="{{ route('admin.phieunhapkho.index') }}" class="btn btn-light me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <i class="fa-solid fa-save me-1"></i> Lưu Phiếu (Bản Nháp)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const materials = @json($nguyenlieus);

    document.addEventListener('DOMContentLoaded', function() {
        addMaterialRow(); // Khởi tạo 1 dòng trống ban đầu
    });

    function addMaterialRow() {
        const tbody = document.getElementById('importBody');
        const rowIndex = tbody.children.length;
        
        let options = '<option value="">-- Chọn --</option>';
        materials.forEach(m => {
            options += `<option value="${m.id}" data-unit="${m.dvt}">${m.ten_nl} (${m.dvt})</option>`;
        });

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="id_nguyen_lieus[]" class="form-select select-material" required onchange="calculateRow(this)">
                    ${options}
                </select>
            </td>
            <td>
                <input type="number" name="quantities[]" class="form-control text-center input-qty" value="1" min="1" required oninput="calculateRow(this)">
            </td>
            <td>
                <input type="number" name="dongias[]" class="form-control text-end input-price" value="0" min="0" required oninput="calculateRow(this)">
            </td>
            <td class="text-end fw-bold text-danger row-subtotal">
                0
            </td>
            <td>
                <input type="date" name="hsds[]" class="form-control">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(this)">
                    <i class="fa-solid fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        calculateGrandTotal();
    }

    function removeRow(button) {
        const tbody = document.getElementById('importBody');
        if (tbody.children.length > 1) {
            button.closest('tr').remove();
            calculateGrandTotal();
        } else {
            alert('Phải có ít nhất 1 mặt hàng để nhập kho!');
        }
    }

    function calculateRow(element) {
        const tr = element.closest('tr');
        const qty = parseFloat(tr.querySelector('.input-qty').value) || 0;
        const price = parseFloat(tr.querySelector('.input-price').value) || 0;
        const subtotal = qty * price;
        
        tr.querySelector('.row-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(subtotal);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('#importBody tr').forEach(tr => {
            const qty = parseFloat(tr.querySelector('.input-qty').value) || 0;
            const price = parseFloat(tr.querySelector('.input-price').value) || 0;
            total += (qty * price);
        });
        document.getElementById('grandTotal').textContent = new Intl.NumberFormat('vi-VN').format(total);
        
        const btnSubmit = document.getElementById('btnSubmit');
        if (total === 0) {
            btnSubmit.disabled = true;
        } else {
            btnSubmit.disabled = false;
        }
    }
</script>
@endsection
