@extends('layouts.admin')

@section('title', 'Lập Phiếu Hủy Hàng')
@section('page_title', 'LẬP PHIẾU HỦY HÀNG')

@section('content')
<div class="container-fluid mt-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-warning py-2 shadow-sm border-0 mb-4 d-flex align-items-center">
        <i class="fa-solid fa-triangle-exclamation fs-5 me-2 text-warning"></i>
        <div class="small">
            <strong>Lưu ý:</strong> Khi lập phiếu, tồn kho sẽ bị trừ ngay để giữ hàng chờ duyệt. Nếu bị từ chối, số lượng sẽ hoàn lại.
        </div>
    </div>

    {{-- JSON data cho JS --}}
    @php
        $loDataForJs = $loNguyenLieus->map(function($lo) {
            return [
                'id'    => $lo->id,
                'malo'  => $lo->malo,
                'tenNl' => $lo->nguyenLieu->ten_nl ?? '',
                'hsd'   => $lo->hsd ? \Carbon\Carbon::parse($lo->hsd)->format('d/m/Y') : 'Không hạn',
                'ton'   => $lo->soluong_hientai,
                // Label ngắn gọn cho select
                'label' => ($lo->nguyenLieu->ten_nl ?? '') . ' — ' . $lo->malo,
            ];
        })->values();
    @endphp
    <script id="lo-nguyen-lieu-data" type="application/json">{!! json_encode($loDataForJs) !!}</script>

    <form action="{{ route('admin.phieuhuyhang.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            {{-- CỘT TRÁI --}}
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom fw-bold text-muted text-uppercase small">
                        <i class="fa-solid fa-circle-info me-1"></i> Thông Tin Chung
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Người lập phiếu</label>
                            <input type="text" class="form-control form-control-sm" value="{{ Auth::guard('nhanvien')->user()->hoten }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Ngày lập</label>
                            <input type="text" class="form-control form-control-sm" value="{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Ghi chú chung</label>
                            <textarea name="ghi_chu_chung" class="form-control form-control-sm" rows="4"
                                placeholder="Ghi chú (nếu có)">{{ old('ghi_chu_chung') }}</textarea>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-3 d-flex gap-2">
                        <a href="{{ route('admin.phieuhuyhang.index') }}" class="btn btn-light border btn-sm flex-fill">Hủy Bỏ</a>
                        <button type="submit" class="btn btn-primary btn-sm flex-fill shadow-sm" id="btn-submit">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Lưu Phiếu
                        </button>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI --}}
            <div class="col-md-8 col-lg-9">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-muted text-uppercase small">
                            <i class="fa-solid fa-list-check me-1"></i> Danh Sách Lô Cần Hủy
                        </span>
                        <button type="button" class="btn btn-sm btn-primary" id="btn-add-row">
                            <i class="fa-solid fa-plus me-1"></i> Thêm Lô
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="items-container">
                            {{-- Các item sẽ được thêm bởi JS --}}
                        </div>
                        <div id="empty-state" class="text-center py-5 text-muted" style="display:none;">
                            <i class="fa-solid fa-box-open fa-3x mb-3 d-block opacity-25"></i>
                            Chưa có lô nào được chọn.<br>
                            <span class="small">Nhấn <strong>"Thêm Lô"</strong> để bắt đầu.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.disposal-item {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 10px;
    transition: border-color 0.2s;
}
.disposal-item:hover { border-color: #adb5bd; }
.disposal-item .item-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 8px;
}
.disposal-item .item-number {
    font-size: 0.75rem;
    font-weight: 700;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.lot-info-badge {
    display: inline-flex;
    gap: 6px;
    font-size: 0.78rem;
    margin-top: 4px;
}
.lot-info-badge .badge { font-weight: 500; }
</style>
@endsection

@push('scripts')
<script>
$(function () {
    var loData = JSON.parse(document.getElementById('lo-nguyen-lieu-data').textContent);
    var preselectedLotId = {{ $preselectedLotId ? (int)$preselectedLotId : 'null' }};
    var rowCount = 0;

    function getLo(id) {
        for (var i = 0; i < loData.length; i++) {
            if (loData[i].id === parseInt(id)) return loData[i];
        }
        return null;
    }

    function buildOptions(selectedId) {
        var html = '<option value="">-- Chọn lô nguyên liệu --</option>';
        for (var i = 0; i < loData.length; i++) {
            var lo = loData[i];
            var sel = (selectedId && parseInt(selectedId) === lo.id) ? ' selected' : '';
            html += '<option value="' + lo.id + '"' + sel + '>' + lo.label + '</option>';
        }
        return html;
    }

    function addRow(selectedId) {
        var idx = rowCount++;
        var selId = 'sel-' + idx;
        var lo = selectedId ? getLo(selectedId) : null;

        var $item = $(
            '<div class="disposal-item" data-idx="' + idx + '">' +
                '<div class="item-header">' +
                    '<span class="item-number">Lô #' + (idx + 1) + '</span>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger btn-remove py-0 px-2">' +
                        '<i class="fa-solid fa-xmark"></i>' +
                    '</button>' +
                '</div>' +
                '<div class="mb-2">' +
                    '<select class="form-select form-select-sm" id="' + selId + '" name="details[' + idx + '][id_lo_nguyen_lieu]" required>' +
                        buildOptions(selectedId) +
                    '</select>' +
                    '<div class="lot-info-badge" id="badge-' + idx + '">' +
                        (lo ? renderBadge(lo) : '') +
                    '</div>' +
                '</div>' +
                '<div class="row g-2">' +
                    '<div class="col-sm-3">' +
                        '<label class="form-label small text-muted mb-1">Tồn hiện tại</label>' +
                        '<input type="text" class="form-control form-control-sm text-center fw-bold text-primary" id="ton-' + idx + '" value="' + (lo ? lo.ton : '') + '" readonly>' +
                    '</div>' +
                    '<div class="col-sm-3">' +
                        '<label class="form-label small text-muted mb-1">Số lượng hủy <span class="text-danger">*</span></label>' +
                        '<input type="number" class="form-control form-control-sm text-center" name="details[' + idx + '][so_luong_huy]" min="1"' + (lo ? ' max="' + lo.ton + '"' : '') + ' required>' +
                    '</div>' +
                    '<div class="col-sm-6">' +
                        '<label class="form-label small text-muted mb-1">Lý do hủy <span class="text-danger">*</span></label>' +
                        '<input type="text" class="form-control form-control-sm" name="details[' + idx + '][ly_do_chi_tiet]" placeholder="VD: Hoa héo, gãy cành..." required>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        $('#items-container').append($item);

        // Khởi tạo Select2
        $('#' + selId).select2({
            theme: 'bootstrap-5',
            placeholder: '-- Chọn lô nguyên liệu --',
            width: '100%'
        }).on('change', function () {
            var newLo = getLo($(this).val());
            var $badge = $('#badge-' + idx);
            var $ton   = $('#ton-' + idx);
            var $qty   = $item.find('input[type=number]');
            if (newLo) {
                $badge.html(renderBadge(newLo));
                $ton.val(newLo.ton);
                $qty.attr('max', newLo.ton).val('');
            } else {
                $badge.html('');
                $ton.val('');
                $qty.removeAttr('max').val('');
            }
        });

        refreshState();
    }

    function renderBadge(lo) {
        return '<span class="badge bg-light text-dark border">' +
                    '<i class="fa-solid fa-barcode me-1 text-muted"></i>' + lo.malo +
               '</span>' +
               '<span class="badge bg-light text-secondary border">' +
                    '<i class="fa-regular fa-calendar me-1"></i>HSD: ' + lo.hsd +
               '</span>';
    }

    function refreshState() {
        var hasRows = $('#items-container .disposal-item').length > 0;
        $('#empty-state').toggle(!hasRows);
        $('#btn-submit').prop('disabled', !hasRows);

        // Cập nhật số thứ tự
        $('#items-container .disposal-item').each(function(i) {
            $(this).find('.item-number').text('Lô #' + (i + 1));
        });
    }

    $('#btn-add-row').on('click', function () {
        addRow(null);
    });

    $('#items-container').on('click', '.btn-remove', function () {
        var $sel = $(this).closest('.disposal-item').find('select');
        if ($sel.data('select2')) $sel.select2('destroy');
        $(this).closest('.disposal-item').remove();
        rowCount--;
        refreshState();
    });

    // Khởi tạo ban đầu
    addRow(preselectedLotId || null);
    refreshState();
});
</script>
@endpush
