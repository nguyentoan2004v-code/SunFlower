@extends('layouts.admin')

@section('title', 'Quản Lý Phân Ca (Theo Tuần)')

@section('content')
<style>
    /* --- CSS CUSTOM CHO BẢNG LỊCH TRỰC --- */
    .schedule-card {
        border-radius: 12px;
        border: none;
        overflow: hidden;
    }
    .schedule-header {
        background: #ffffff;
        border-bottom: 2px solid #f0f2f5;
        padding: 20px;
    }
    .nav-week-btn {
        border-radius: 20px;
        padding: 6px 20px;
        font-weight: 600;
        border: 1px solid #e0e6ed;
        color: #4a5568;
        background: white;
        transition: all 0.2s;
    }
    .nav-week-btn:hover {
        background: var(--sunflower-orange, #FF8C00);
        color: white;
        border-color: var(--sunflower-orange, #FF8C00);
    }
    .table-schedule {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .table-schedule thead th {
        background-color: #2c3e50; /* Màu dark blue sang trọng */
        color: #ffffff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 15px 10px;
        border: none;
    }
    .table-schedule tbody td {
        vertical-align: middle;
        padding: 12px 8px;
        border-bottom: 1px solid #f0f2f5;
        border-right: 1px dashed #f0f2f5;
    }
    .table-schedule tbody td:last-child {
        border-right: none;
    }
    .table-schedule tbody tr:hover {
        background-color: #fafbfc;
    }
    
    /* Cột thông tin nhân viên */
    .employee-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .employee-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: #edf2f7;
        color: var(--sunflower-orange, #FF8C00);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1rem;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .employee-name {
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        font-size: 0.95rem;
    }
    .employee-id {
        font-size: 0.75rem;
        color: #a0aec0;
        margin: 0;
    }

    /* Styling cho ô Select (Dropdown) */
    .shift-select {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 8px 25px 8px 12px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        cursor: pointer;
        width: 100%;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23a0aec0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 12px 10px;
    }
    .shift-select:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.15);
        border-color: var(--sunflower-orange, #FF8C00);
    }
    
    /* Trạng thái đã chọn ca (Màu cam/vàng pastel mềm mại) */
    .shift-selected {
        background-color: #fffaf0 !important; 
        color: #dd6b20 !important; 
        border-color: #fbd38d !important;
        font-weight: 700;
    }
    /* Trạng thái Nghỉ (Trắng/Xám nhạt) */
    .shift-empty {
        background-color: #ffffff;
        color: #a0aec0;
    }

    /* Nút Lưu */
    .btn-save-schedule {
        background: linear-gradient(135deg, var(--sunflower-orange, #FF8C00) 0%, #e67e00 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 12px 30px;
        font-weight: bold;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(255, 140, 0, 0.25);
        transition: all 0.3s;
    }
    .btn-save-schedule:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(255, 140, 0, 0.35);
        color: white;
    }
</style>

<div class="container-fluid mt-4">
    <div class="card shadow-sm schedule-card mb-4">
        
        <!-- Thanh điều hướng Tuần -->
        <div class="schedule-header d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.lichlamviec.index', ['week' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="text-decoration-none nav-week-btn">
                <i class="fas fa-chevron-left me-1"></i> Tuần trước
            </a>
            <div class="text-center">
                <h5 class="fw-bold text-dark m-0 mb-1">
                    <i class="fa-regular fa-calendar-days text-warning me-2"></i>LỊCH LÀM VIỆC
                </h5>
                <span class="badge bg-light text-dark border px-3 py-2" style="font-size: 0.85rem;">
                    {{ $startOfWeek->format('d/m/Y') }} - {{ $startOfWeek->copy()->endOfWeek()->format('d/m/Y') }}
                </span>
            </div>
            <a href="{{ route('admin.lichlamviec.index', ['week' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="text-decoration-none nav-week-btn">
                Tuần sau <i class="fas fa-chevron-right ms-1"></i>
            </a>
        </div>

        <div class="card-body p-0">
            @if(session('success')) <div class="alert alert-success m-3">{{ session('success') }}</div> @endif
            @if(session('error')) <div class="alert alert-danger m-3">{{ session('error') }}</div> @endif

            <form action="{{ route('admin.lichlamviec.saveWeekly') }}" method="POST">
                @csrf
                <input type="hidden" name="current_week" value="{{ $startOfWeek->format('Y-m-d') }}">
                
                <div class="table-responsive">
                    <table class="table table-schedule w-100">
                        <thead>
                            <tr>
                                <th style="width: 18%; padding-left: 20px;">Nhân viên</th>
                                @foreach($days as $day)
                                    <th class="text-center">
                                        <div style="font-size: 0.95rem;">{{ $day->translatedFormat('l') }}</div>
                                        <div class="text-white-50 mt-1" style="font-weight: normal; font-size: 0.75rem;">{{ $day->format('d/m') }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nhanviens as $nv)
                                <tr>
                                    <td style="padding-left: 20px;">
                                        <div class="employee-info">
                                            <!-- Lấy chữ cái đầu tiên của tên làm Avatar -->
                                            <div class="employee-avatar">
                                                {{ mb_substr($nv->hoten, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="employee-name">{{ $nv->hoten }}</p>
                                                <p class="employee-id">{{ $nv->manv }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($days as $day)
                                        @php $dateStr = $day->format('Y-m-d'); @endphp
                                        <td class="text-center">
                                            <select name="phancong[{{ $nv->manv }}][{{ $dateStr }}]" class="shift-select" onchange="highlight(this)">
                                                <option value="" class="shift-empty">-- Nghỉ --</option>
                                                @foreach($caLamViecs as $ca)
                                                    <option value="{{ $ca->maca }}" 
                                                        {{ isset($matrix[$nv->manv][$dateStr]) && $matrix[$nv->manv][$dateStr] == $ca->maca ? 'selected' : '' }}>
                                                        {{ $ca->maca }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-light border-top text-end">
                    <button type="submit" class="btn-save-schedule">
                        <i class="fas fa-save me-2"></i> LƯU LỊCH TUẦN NÀY
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // JS xử lý hiệu ứng UI khi chọn ca
    function highlight(selectElement) {
        if (selectElement.value !== "") {
            selectElement.classList.remove('shift-empty');
            selectElement.classList.add('shift-selected');
        } else {
            selectElement.classList.remove('shift-selected');
            selectElement.classList.add('shift-empty');
        }
    }

    // Chạy khi load trang
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.shift-select').forEach(function(select) {
            highlight(select);
        });
    });
</script>
@endsection