@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')
@section('page_title', 'CHI TIẾT ĐƠN HÀNG: ' . $order->madon)

@section('content')
<style>
    /* ==========================================
       BỔ SUNG DARK MODE CHO CHI TIẾT ĐƠN HÀNG
       ========================================== */
    [data-bs-theme="dark"] .card { background-color: #212529 !important; border: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .card-header.bg-white { background-color: #2c3034 !important; border-bottom: 1px solid #373b3e !important; }
    [data-bs-theme="dark"] .text-dark, [data-bs-theme="dark"] .text-primary, [data-bs-theme="dark"] p strong, [data-bs-theme="dark"] .form-label { color: #e9ecef !important; }
    [data-bs-theme="dark"] p { color: #dee2e6 !important; }
    [data-bs-theme="dark"] hr { border-color: #495057 !important; }
    
    /* CSS Table */
    [data-bs-theme="dark"] .table { color: #e9ecef !important; }
    [data-bs-theme="dark"] .table-light th { background-color: #1a1d20 !important; color: #adb5bd !important; border-bottom: 2px solid #373b3e !important; }
    [data-bs-theme="dark"] .table td, [data-bs-theme="dark"] .table th { border-color: #373b3e !important; }

    /* Khung Hóa Đơn đã in */
    [data-bs-theme="dark"] .alert-success { background-color: #051b11 !important; border: 1px solid #0f5132 !important; color: #75b798 !important; }
    [data-bs-theme="dark"] .alert-success .text-muted { color: #a3b8ac !important; }
    [data-bs-theme="dark"] .btn-light { background-color: #2c3034 !important; color: #dee2e6 !important; border-color: #495057 !important; }
    [data-bs-theme="dark"] .btn-light:hover { background-color: #373b3e !important; color: #ffffff !important; }
</style>

<div class="container-fluid mt-3 pb-5">
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm"><i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none text-secondary">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fa-solid fa-truck-fast me-2"></i>Xử lý Đơn hàng</h6>
                </div>
                <div class="card-body">
                    <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->ngaydat)->format('d/m/Y H:i') }}</p>
                    
                    {{-- Mượn tên từ bảng khachhang (Giả sử cột tên trong bảng khách hàng là 'tenkh' hoặc 'hoten') --}}
                    <p><strong>Người nhận:</strong> {{  $order->khachhang->hoten ?? 'Khách vãng lai' }}</p>
                    
                    <p><strong>Điện thoại:</strong> {{ $order->sdt_nhan }}</p>
                    
                    {{-- Đổi thành diachi_giao cho khớp với Database --}}
                    <p><strong>Địa chỉ:</strong> {{ $order->diachi_giao }}</p>
                    
                    <p><strong>Ghi chú:</strong> {{ $order->ghichu ?? 'Không có' }}</p>
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Trạng thái hiện tại:</label>
                        <div class="mt-1">
                            @if($order->trangthai == 'Chờ xác nhận')
                                <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">Chờ xác nhận</span>
                            @elseif($order->trangthai == 'Đã xác nhận')
                                <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">Đã xác nhận</span>
                            @elseif($order->trangthai == 'Đang giao')
                                <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill">Đang giao</span>
                            @elseif($order->trangthai == 'Đã hoàn thành')
                                <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">Đã hoàn thành</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill">{{ $order->trangthai }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Xử lý nút bấm tuần tự --}}
                    @if($order->trangthai != 'Đã hoàn thành' && $order->trangthai != 'Đã hủy')
                        <hr>
                        <label class="form-label fw-bold">Thao tác:</label>
                        <div class="d-grid gap-2">
                            
                            @if($order->trangthai == 'Chờ xác nhận')
                                <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="trangthai" value="Đã xác nhận">
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                                        <i class="fa-solid fa-clipboard-check me-2"></i> Xác nhận đơn hàng
                                    </button>
                                </form>
                            @endif

                            @if($order->trangthai == 'Đã xác nhận')
                                <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="trangthai" value="Đang giao">
                                    <button type="submit" class="btn text-dark w-100 fw-bold" style="background-color: #0dcaf0;">
                                        <i class="fa-solid fa-truck-arrow-right me-2"></i> Chuyển giao hàng
                                    </button>
                                </form>
                            @endif

                            @if($order->trangthai == 'Đang giao')
                                <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="trangthai" value="Đã hoàn thành">
                                    <button type="submit" class="btn btn-success w-100 fw-bold">
                                        <i class="fa-solid fa-check-double me-2"></i> Xác nhận Đã hoàn thành
                                    </button>
                                </form>
                            @endif

                            {{-- Nút Hủy đơn --}}
                            @if($order->trangthai == 'Chờ xác nhận')
                                {{-- Hủy sớm: Đơn giản, chỉ cần xác nhận --}}
                                <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?\nNguyên liệu sẽ được hoàn trả về kho.');">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="trangthai" value="Đã hủy">
                                    <button type="submit" class="btn btn-outline-secondary w-100">
                                        <i class="fa-solid fa-ban me-2"></i> Hủy đơn hàng
                                    </button>
                                </form>
                            @else
                                {{-- Hủy muộn: Modal cảnh báo + bắt buộc nhập lý do --}}
                                <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#lateCancelModal">
                                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Hủy đơn hàng (Đã sử dụng NL)
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Hiển thị thông tin khi đơn đã bị hủy muộn --}}
                    @if($order->trangthai == 'Đã hủy' && $order->ly_do_huy)
                        <hr>
                        <div class="alert alert-danger mb-0 border-0 shadow-sm">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-1"></i> Đơn hàng đã hủy muộn</h6>
                            <p class="mb-2"><strong>Lý do:</strong> {{ $order->ly_do_huy }}</p>
                            <hr class="my-2">
                            <p class="mb-1 small fw-bold"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Hướng xử lý bó hoa đã cắm:</p>
                            <ul class="small mb-0">
                                <li>Trưng bày bán lại cho khách vãng lai (giảm 10-30%)</li>
                                <li>Nếu không bán được trong 1-2 ngày → <a href="{{ route('admin.phieuhuyhang.create') }}" class="text-decoration-none">Lập phiếu hủy hàng</a></li>
                            </ul>
                        </div>
                    @endif

                    @if(!$hoadon)
                        {{-- Chỉ cho phép xuất hóa đơn khi đơn hàng đã hoàn thành hoặc đang giao --}}
                        @if(in_array($order->trangthai, ['Đã hoàn thành']))
                            <div class="mt-4">
                                <form action="{{ route('admin.orders.export-invoice', $order->madon) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 fw-bold">
                                        <i class="fa-solid fa-file-invoice me-2"></i> Xuất Hóa Đơn
                                    </button>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-success mt-4 mb-0 border-0 shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fa-solid fa-receipt me-2"></i> <strong>{{ $hoadon->mahd }}</strong>
                                    <br><small class="text-muted">Lập ngày: {{ \Carbon\Carbon::parse($hoadon->ngaylap)->format('d/m/Y') }}</small>
                                </div>
                                <a href="{{ route('admin.orders.print-invoice', $hoadon->mahd) }}" target="_blank" class="btn btn-light btn-sm border">
                                    <i class="fa-solid fa-print"></i> In
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold" style="color: var(--sunflower-orange);"><i class="fa-solid fa-box-open me-2"></i>Chi tiết sản phẩm</h6>
                    <h5 class="m-0 text-danger fw-bold">Tổng: {{ number_format($order->tongtien, 0, ',', '.') }} ₫</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Sản phẩm</th>
                                    <th class="text-center">Đơn giá</th>
                                    <th class="text-center">SL</th>
                                    <th class="text-end pe-4">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->chiTietDonHangs as $ct)
                                    @php
                                        $sp = $ct->sanpham;
                                        $spImg = asset('images/bg-sunflower.jpg');
                                        if($sp && !empty($sp->hinhanh)){
                                            $spImg = str_starts_with($sp->hinhanh, 'http') ? $sp->hinhanh : asset('storage/' . ltrim($sp->hinhanh, '/'));
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $spImg }}" class="rounded shadow-sm me-3" style="width:50px; height:50px; object-fit:cover;">
                                                <div>
                                                    <span class="fw-medium">{{ $sp->tensp ?? $ct->masp }}</span>
                                                    
                                                    {{-- Hiển thị danh sách nguyên liệu BOM đã chốt cho đơn này --}}
                                                    @if($ct->chiTietDonHangNguyenLieus->count() > 0)
                                                        <div class="mt-2 p-2 bg-light rounded border border-light-subtle" style="max-width: 600px;">
                                                            <div class="small fw-bold text-success mb-2 ps-1">
                                                                <i class="fa-solid fa-leaf me-1"></i> Chi tiết Nguyên liệu sử dụng:
                                                            </div>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-borderless align-middle mb-0" style="font-size: 0.85rem;">
                                                                    <tbody>
                                                                        @foreach($ct->chiTietDonHangNguyenLieus as $oim)
                                                                            <tr>
                                                                                <td style="width: 45%; padding-left: 10px;">
                                                                                    <span class="fw-medium text-dark">{{ $oim->nguyenLieu->ten_nl ?? 'N/A' }}</span>
                                                                                </td>
                                                                                <td style="width: 10%;" class="text-center">
                                                                                    <span class="badge bg-secondary-subtle text-secondary border">x {{ $oim->soluong_dung }}</span>
                                                                                </td>
                                                                                <td>
                                                                                    {{-- Lô lấy hàng --}}
                                                                                    @if($order->trangthai != 'Chờ xác nhận' && $order->trangthai != 'Đã hủy')
                                                                                        @if($oim->pickedLots && $oim->pickedLots->count() > 0)
                                                                                            <div class="d-flex align-items-center gap-1 flex-wrap justify-content-end">
                                                                                                @foreach($oim->pickedLots as $pickedLot)
                                                                                                    <span class="badge bg-white text-primary border border-primary-subtle shadow-sm">
                                                                                                        {{ $pickedLot->loNguyenLieu->malo ?? 'N/A' }} 
                                                                                                        <span class="text-danger ms-1">(-{{ $pickedLot->soluong }})</span>
                                                                                                    </span>
                                                                                                @endforeach
                                                                                                
                                                                                                @if(in_array($order->trangthai, ['Đã xác nhận', 'Đang giao']))
                                                                                                    <button type="button" class="btn btn-sm btn-light border shadow-sm p-1 text-muted" style="line-height: 1;" title="Đổi lô khác" onclick="openAdjustLotModal({{ $oim->id }}, '{{ $oim->nguyenLieu->ten_nl ?? '' }}', {{ json_encode($oim->pickedLots->load('loNguyenLieu')) }}, {{ $oim->id_nguyen_lieu }}, {{ $oim->soluong_dung }})">
                                                                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                                                                    </button>
                                                                                                @endif
                                                                                            </div>
                                                                                        @else
                                                                                            <div class="text-end text-muted fst-italic" style="font-size: 0.8rem;">
                                                                                                <i class="fa-solid fa-clock-rotate-left me-1"></i> Dữ liệu cũ
                                                                                            </div>
                                                                                        @endif
                                                                                    @else
                                                                                        <div class="text-end text-muted fst-italic" style="font-size: 0.8rem;">
                                                                                            Chờ phân bổ
                                                                                        </div>
                                                                                    @endif
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            
                                                            @if($order->trangthai == 'Chờ xác nhận')
                                                                <div class="text-end mt-2 pt-2 border-top border-light-subtle">
                                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="openAdjustModal({{ $ct->id }}, {{ json_encode($ct->chiTietDonHangNguyenLieus->load('nguyenLieu')) }})">
                                                                        <i class="fa-solid fa-pen-to-square me-1"></i> Điều chỉnh nguyên liệu
                                                                    </button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ number_format($ct->giaban, 0, ',', '.') }} ₫</td>
                                        <td class="text-center fw-bold">{{ $ct->soluong }}</td>
                                        <td class="text-end pe-4 fw-bold text-danger">{{ number_format($ct->giaban * $ct->soluong, 0, ',', '.') }} ₫</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL ĐIỀU CHỈNH NGUYÊN LIỆU --}}
<div class="modal fade" id="adjustMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="adjustMaterialForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-ruler me-2"></i>Điều chỉnh Thiết kế (Nguyên liệu)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <i class="fa-solid fa-circle-info me-1"></i> Điều chỉnh này chỉ áp dụng cho sản phẩm trong đơn hàng này.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle" id="adjustBomTable">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Nguyên liệu</th>
                                    <th style="width: 30%;">Số lượng sử dụng</th>
                                    <th style="width: 10%;">Đơn vị</th>
                                    <th style="width: 10%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="adjustBomBody">
                                {{-- Load bằng JS --}}
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAdjustRow()">
                        <i class="fa-solid fa-plus"></i> Thêm nguyên liệu
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const allNguyenLieus = @json($allNguyenLieus);
    let adjustModal = null;

    document.addEventListener('DOMContentLoaded', function() {
        adjustModal = new bootstrap.Modal(document.getElementById('adjustMaterialModal'));
    });

    function openAdjustModal(detailId, currentMaterials) {
        const form = document.getElementById('adjustMaterialForm');
        form.action = `/admin/orders/{{ $order->madon }}/adjust-materials/${detailId}`;
        
        const tbody = document.getElementById('adjustBomBody');
        tbody.innerHTML = '';
        
        currentMaterials.forEach(oim => {
            addAdjustRow(oim.id_nguyen_lieu, oim.soluong_dung);
        });
        
        if (currentMaterials.length === 0) {
            addAdjustRow();
        }
        
        adjustModal.show();
    }

    function addAdjustRow(selectedId = '', qty = 1) {
        const tbody = document.getElementById('adjustBomBody');
        const row = document.createElement('tr');
        
        let options = '<option value="">-- Chọn nguyên liệu --</option>';
        let selectedUnit = '-';
        
        allNguyenLieus.forEach(m => {
            const isSelected = m.id == selectedId ? 'selected' : '';
            if (isSelected) selectedUnit = m.dvt;
            options += `<option value="${m.id}" data-unit="${m.dvt}" ${isSelected}>${m.ten_nl} (${m.dvt})</option>`;
        });

        row.innerHTML = `
            <td>
                <select name="id_nguyen_lieus[]" class="form-select bom-select" required onchange="updateAdjustUnit(this)">
                    ${options}
                </select>
            </td>
            <td>
                <input type="number" name="quantities[]" class="form-control" value="${qty}" min="1" required>
            </td>
            <td>
                <span class="bom-unit fw-bold text-muted">${selectedUnit}</span>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">
                    <i class="fa-solid fa-times"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function updateAdjustUnit(selectElement) {
        const unitSpan = selectElement.closest('tr').querySelector('.bom-unit');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        if (selectedOption.value) {
            unitSpan.textContent = selectedOption.getAttribute('data-unit');
        } else {
            unitSpan.textContent = '-';
        }
    }
</script>

{{-- MODAL HỦY ĐƠN MUỘN --}}
@if($order->trangthai != 'Đã hoàn thành' && $order->trangthai != 'Đã hủy' && $order->trangthai != 'Chờ xác nhận')
<div class="modal fade" id="lateCancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.orders.update', $order->madon) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="trangthai" value="Đã hủy">
                
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Cảnh báo: Hủy đơn đã xử lý
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="alert alert-warning border-0 shadow-sm mb-3">
                        <i class="fa-solid fa-exclamation-circle me-1"></i>
                        <strong>Nguyên liệu đã được sử dụng để cắm hoa và KHÔNG thể hoàn trả về kho.</strong>
                        <p class="mb-0 mt-1 small">Tồn kho sẽ không thay đổi. Bó hoa đã cắm cần được xử lý riêng.</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            Lý do hủy đơn <span class="text-danger">*</span>
                        </label>
                        <textarea name="ly_do_huy" class="form-control" rows="3" required 
                                  placeholder="VD: Khách gọi điện hủy vì thay đổi kế hoạch..."></textarea>
                    </div>

                    <div class="card bg-light border-0">
                        <div class="card-body py-2 px-3">
                            <p class="mb-1 small fw-bold"><i class="fa-solid fa-lightbulb text-warning me-1"></i> Sau khi hủy, hãy xử lý bó hoa đã cắm:</p>
                            <ul class="small mb-0 ps-3">
                                <li>Trưng bày bán lại cho khách vãng lai (giảm 10-30%)</li>
                                <li>Nếu không bán được trong 1-2 ngày → Lập phiếu hủy hàng</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger fw-bold">
                        <i class="fa-solid fa-ban me-1"></i> Xác nhận hủy đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- MODAL ĐIỀU CHỈNH LÔ LẤY HÀNG --}}
<div class="modal fade" id="adjustLotModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="adjustLotForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-boxes-stacked me-2"></i> Điều chỉnh Lô lấy hàng</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <i class="fa-solid fa-circle-info me-1"></i> Đang điều chỉnh lô cho nguyên liệu: <strong id="adjustLotMaterialName" class="text-primary"></strong>
                        <br>Số lượng cần lấy: <strong id="adjustLotRequiredQty" class="text-danger"></strong>
                    </div>

                    <div class="table-responsive bg-white rounded shadow-sm border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã lô</th>
                                    <th>Hạn sử dụng</th>
                                    <th class="text-center">Tồn khả dụng</th>
                                    <th class="text-center" style="width: 150px;">SL Lấy</th>
                                </tr>
                            </thead>
                            <tbody id="availableLotsTbody">
                                <tr><td colspan="4" class="text-center py-3 text-muted">Đang tải danh sách lô...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 text-end">
                        Tổng SL đã chọn: <strong id="totalSelectedLotQty" class="text-danger fs-5">0</strong> / <span id="targetLotQty" class="fw-bold">0</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitAdjustLot" disabled>
                        <i class="fa-solid fa-check me-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentRequiredQty = 0;
    
    function openAdjustLotModal(oimId, materialName, currentPickedLots, materialId, requiredQty) {
        document.getElementById('adjustLotMaterialName').textContent = materialName;
        document.getElementById('adjustLotRequiredQty').textContent = requiredQty;
        document.getElementById('targetLotQty').textContent = requiredQty;
        currentRequiredQty = requiredQty;
        
        // Set form action dynamically
        document.getElementById('adjustLotForm').action = `/admin/orders/{{ $order->madon }}/adjust-lots/${oimId}`;
        
        // Show modal immediately with loading state
        const modal = new bootstrap.Modal(document.getElementById('adjustLotModal'));
        modal.show();
        
        // Fetch available lots via AJAX
        fetch(`/admin/inventory/lots/available/${materialId}`)
            .then(response => response.json())
            .then(data => {
                renderLotsTable(data.lots, currentPickedLots);
                validateLotSelection();
            })
            .catch(error => {
                console.error('Error fetching lots:', error);
                document.getElementById('availableLotsTbody').innerHTML = `<tr><td colspan="4" class="text-center text-danger py-3">Lỗi khi tải danh sách lô!</td></tr>`;
            });
    }

    function renderLotsTable(availableLots, currentPickedLots) {
        const tbody = document.getElementById('availableLotsTbody');
        tbody.innerHTML = '';
        
        if (availableLots.length === 0) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">Không có lô nào còn hàng.</td></tr>`;
            return;
        }

        // Tạo map các lô đã chọn để dễ fill giá trị
        const pickedMap = {};
        currentPickedLots.forEach(picked => {
            pickedMap[picked.id_lo] = picked.soluong;
        });

        availableLots.forEach(lot => {
            // SL thực tế = SL tồn khả dụng hiện tại + SL đã lấy (nếu lô này đang được chọn)
            const currentlyPicked = pickedMap[lot.id] || 0;
            const trueAvailable = lot.soluong_hientai + currentlyPicked;
            
            // Format HSD
            let hsdStr = 'Không có';
            if (lot.hsd) {
                const date = new Date(lot.hsd);
                hsdStr = date.toLocaleDateString('vi-VN');
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="fw-medium">${lot.malo}</td>
                <td>${hsdStr}</td>
                <td class="text-center fw-bold text-success">${trueAvailable}</td>
                <td class="text-center">
                    <input type="number" name="lots[${lot.id}]" class="form-control form-control-sm text-center lot-input" 
                           min="0" max="${trueAvailable}" value="${currentlyPicked}" onchange="validateLotSelection()" onkeyup="validateLotSelection()">
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function validateLotSelection() {
        const inputs = document.querySelectorAll('.lot-input');
        let total = 0;
        let hasError = false;
        
        inputs.forEach(input => {
            let val = parseInt(input.value) || 0;
            let max = parseInt(input.getAttribute('max')) || 0;
            
            if (val < 0) val = 0;
            if (val > max) {
                input.classList.add('is-invalid');
                hasError = true;
            } else {
                input.classList.remove('is-invalid');
            }
            
            total += val;
        });
        
        const totalEl = document.getElementById('totalSelectedLotQty');
        totalEl.textContent = total;
        
        const btnSubmit = document.getElementById('btnSubmitAdjustLot');
        
        if (total === currentRequiredQty && !hasError) {
            totalEl.classList.remove('text-danger');
            totalEl.classList.add('text-success');
            btnSubmit.disabled = false;
        } else {
            totalEl.classList.remove('text-success');
            totalEl.classList.add('text-danger');
            btnSubmit.disabled = true;
        }
    }
</script>

@endsection
