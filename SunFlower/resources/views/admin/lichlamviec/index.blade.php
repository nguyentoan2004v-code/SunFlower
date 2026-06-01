@extends('layouts.admin')

@section('title', 'Quản Lý Phân Ca (Theo Tuần)')

@section('content')
<style>
    /* ==========================================
       1. COMPONENT CHÍNH (THẺ, BẢNG)
       ========================================== */
    .schedule-card { border-radius: 12px; border: none; overflow: hidden; }
    .schedule-header { background: #ffffff; border-bottom: 2px solid #f0f2f5; padding: 20px; }
    .table-schedule { margin-bottom: 0; border-collapse: separate; border-spacing: 0; }
    .table-schedule thead th { background-color: #2c3e50; color: #ffffff; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; padding: 15px 10px; border: none; }
    .table-schedule tbody td { vertical-align: middle; padding: 12px 8px; border-bottom: 1px solid #f0f2f5; border-right: 1px dashed #f0f2f5; }
    .table-schedule tbody td:last-child { border-right: none; }
    .table-schedule tbody tr:hover { background-color: #fafbfc; }

    /* ==========================================
       2. THÔNG TIN NHÂN VIÊN
       ========================================== */
    .employee-info { display: flex; align-items: center; gap: 12px; }
    .employee-avatar { width: 38px; height: 38px; border-radius: 50%; background-color: #edf2f7; color: var(--sunflower-orange, #FF8C00); display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1rem; border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .employee-name { font-weight: 700; color: #2d3748; margin: 0; font-size: 0.95rem; }
    .employee-id { font-size: 0.75rem; color: #a0aec0; margin: 0; }

    /* ==========================================
       3. Ô CHỌN CA (DROPDOWN) & TRẠNG THÁI CA
       ========================================== */
    .shift-select { border-radius: 8px; border: 1px solid #e2e8f0; padding: 8px 25px 8px 12px; font-size: 0.85rem; transition: all 0.2s ease; cursor: pointer; width: 100%; appearance: none; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='none' stroke='%23a0aec0' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 0.75rem center/12px 10px; }
    .shift-select:focus { outline: none; box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.15); border-color: var(--sunflower-orange, #FF8C00); }
    .shift-sang { background-color: #fffaf0 !important; color: #9c4221 !important; border-color: #fbd38d !important; font-weight: 700; }
    .shift-toi { background-color: #ebf8ff !important; color: #2a4365 !important; border-color: #90cdf4 !important; font-weight: 700; }
    .shift-hc { background-color: #f0fff4 !important; color: #22543d !important; border-color: #9ae6b4 !important; font-weight: 700; }
    .shift-empty { background-color: #ffffff; color: #a0aec0; }

    /* ==========================================
       4. NÚT BẤM (BUTTONS)
       ========================================== */
    .nav-week-btn { border-radius: 20px; padding: 6px 20px; font-weight: 600; border: 1px solid #e0e6ed; color: #4a5568; background: white; transition: all 0.2s; }
    .nav-week-btn:hover { background: var(--sunflower-orange, #FF8C00); color: white; border-color: var(--sunflower-orange, #FF8C00); }
    .btn-save-schedule { background: linear-gradient(135deg, var(--sunflower-orange, #FF8C00) 0%, #e67e00 100%); color: white; border: none; border-radius: 8px; padding: 12px 30px; font-weight: bold; letter-spacing: 0.5px; box-shadow: 0 4px 10px rgba(255, 140, 0, 0.25); transition: all 0.3s; }
    .btn-save-schedule:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(255, 140, 0, 0.35); color: white; }

    /* ==========================================
       5. DARK MODE (GIAO DIỆN TỐI)
       ========================================== */
    /* Nền & Viền */
    [data-bs-theme="dark"] .schedule-header, [data-bs-theme="dark"] .p-4.bg-light { background-color: #212529 !important; border-color: #373b3e !important; }
    [data-bs-theme="dark"] .schedule-header .badge.border, [data-bs-theme="dark"] .nav-week-btn { background-color: #2c3034 !important; border-color: #495057 !important; color: #dee2e6 !important; }
    
    /* Màu chữ */
    [data-bs-theme="dark"] .schedule-header .text-dark { color: #f8f9fa !important; }
    [data-bs-theme="dark"] .employee-name, [data-bs-theme="dark"] .shift-select { color: #ffffff !important; }
    [data-bs-theme="dark"] .employee-id { color: #adb5bd !important; }
    
    /* Trạng thái Hover / Ô Ca Làm */
    [data-bs-theme="dark"] .nav-week-btn:hover { background-color: var(--sunflower-orange, #FF8C00) !important; color: #ffffff !important; border-color: var(--sunflower-orange, #FF8C00) !important; }
    [data-bs-theme="dark"] .shift-sang, [data-bs-theme="dark"] .shift-toi, [data-bs-theme="dark"] .shift-hc, [data-bs-theme="dark"] .shift-empty { background-color: #343a40 !important; color: #ffffff !important; border-color: #495057 !important; }
</style>

<div class="container-fluid mt-4">
    <div class="card shadow-sm schedule-card mb-4">
        
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

                <div class="p-4 bg-light border-top d-flex justify-content-end gap-3">
                    <button type="button" onclick="if(confirm('Hệ thống sẽ tự động xếp lịch. Admin làm HC từ T2-T7, nhân viên random 1 ngày nghỉ và random ca sáng/tối. Bạn có chắc chắn? (Lịch cũ tuần này sẽ bị xóa)')) document.getElementById('auto-generate-form').submit();" class="btn text-white" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 8px; padding: 12px 25px; font-weight: bold; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);">
                        <i class="fa-solid fa-wand-magic-sparkles me-2"></i> TỰ ĐỘNG XẾP LỊCH
                    </button>

                    <button type="submit" class="btn-save-schedule">
                        <i class="fas fa-save me-2"></i> LƯU LỊCH TUẦN NÀY
                    </button>
                </div>
            </form>

            <form id="auto-generate-form" action="{{ route('admin.lichlamviec.autoGenerate') }}" method="POST" style="display: none;">
                @csrf
                <input type="hidden" name="week" value="{{ $startOfWeek->format('Y-m-d') }}">
            </form>

        </div>
    </div>
</div>

<script>
    function highlight(selectElement) {
        // Xóa hết class màu cũ
        selectElement.classList.remove('shift-empty', 'shift-sang', 'shift-toi', 'shift-hc', 'shift-selected');
        
        const val = selectElement.value;
        if (val === "CA_SANG") {
            selectElement.classList.add('shift-sang');
        } else if (val === "CA_TOI") {
            selectElement.classList.add('shift-toi');
        } else if (val === "CA_HC") {
            selectElement.classList.add('shift-hc');
        } else if (val !== "") {
            selectElement.classList.add('shift-selected'); // Dự phòng nếu có ca khác
        } else {
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