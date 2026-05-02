@extends('layouts.admin')

@section('title', 'Lịch Làm Việc')

@section('content')
<style>
    /* Tổng thể bảng */
    .excel-container {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06); 
        overflow: hidden;
        border: 1px solid #edf2f7;
    }
    
    .table-excel {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    
    /* ÉP NHỎ CHIỀU CAO VÀ KÍCH THƯỚC Ô */
    .table-excel th, .table-excel td {
        border: 1px solid #e2e8f0;
        text-align: center;
        vertical-align: middle;
        width: 12.5%;
        height: 42px; /* GIẢM TỪ 55px xuống 42px */
        padding: 0;
    }

    /* Header (Thứ & Ngày) */
    .table-excel thead th {
        background-color: #f8fafc; 
        color: #4a5568;
        font-weight: 700;
        padding: 8px 4px; /* Giảm padding */
        text-transform: uppercase;
        font-size: 0.8rem; /* Thu nhỏ chữ */
        border-bottom: 2px solid #e2e8f0;
    }
    
    /* Ngày hôm nay */
    .today-header {
        background-color: #fffaf0 !important;
        color: var(--sunflower-orange, #FF8C00) !important;
        border-bottom: 2px solid var(--sunflower-orange, #FF8C00) !important;
    }

    /* Cột Giờ */
    .time-col {
        background-color: #f8fafc;
        font-weight: 600;
        color: #718096;
        font-size: 0.75rem; /* Thu nhỏ chữ khung giờ */
    }

    /* --- HIỆU ỨNG CA TRỰC --- */
    .cell-active {
        background-color: rgba(255, 140, 0, 0.1) !important; 
        border-left: 4px solid var(--sunflower-orange, #FF8C00) !important; 
        border-right: 1px solid #e2e8f0 !important;
        border-bottom: none !important; 
        border-top: none !important;
    }
    
    .cell-start { border-top: 1px solid rgba(255, 140, 0, 0.2) !important; }
    .cell-end { border-bottom: 1px solid rgba(255, 140, 0, 0.2) !important; }
    .cell-start.cell-end { border-bottom: 1px solid rgba(255, 140, 0, 0.2) !important; }

    /* Tên Ca Trực */
    .shift-label {
        color: #c05600; 
        font-weight: 800;
        font-size: 0.85rem; /* Thu nhỏ chữ tên ca */
        display: inline-block;
        padding-top: 2px;
    }
    
    .table-excel tbody tr:hover td:not(.cell-active):not(.time-col) {
        background-color: #f8fafc;
    }

    .text-sunflower { color: var(--sunflower-orange, #FF8C00); }
</style>

<!-- Giảm margin top/bottom để tiết kiệm không gian màn hình -->
<div class="container-fluid mt-3 mb-4">
    
    <!-- Tiêu đề và Điều hướng -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.lichlamviec.mySchedule', ['week' => $startOfWeek->copy()->subWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm fw-bold">
            <i class="fas fa-arrow-left"></i> Tuần trước
        </a>
        
        <h5 class="fw-bold m-0 text-sunflower text-uppercase" style="letter-spacing: 0.5px; font-size: 1.1rem;">
            <i class="fas fa-calendar-alt me-1"></i> Lịch Làm Việc 
            <span class="text-muted fw-normal text-lowercase fs-6 ms-1">({{ $startOfWeek->format('d/m') }} - {{ $startOfWeek->copy()->endOfWeek()->format('d/m') }})</span>
        </h5>
        
        <a href="{{ route('admin.lichlamviec.mySchedule', ['week' => $startOfWeek->copy()->addWeek()->format('Y-m-d')]) }}" class="btn btn-outline-secondary btn-sm fw-bold">
            Tuần sau <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <!-- Bảng Thời Khóa Biểu -->
    <div class="excel-container">
        <div class="table-responsive">
            <table class="table-excel">
                <thead>
                    <tr>
                        <th class="time-col border-bottom-0">Khung Giờ</th>
                        @foreach($days as $day)
                            <th class="{{ $day->isToday() ? 'today-header' : '' }}">
                                {{ $day->isToday() ? 'Hôm nay' : $day->translatedFormat('l') }} <br>
                                <span class="fw-normal text-muted" style="font-size: 0.7rem; text-transform: none;">{{ $day->format('d/m/Y') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for($h = 7; $h <= 19; $h++)
                        <tr>
                            <td class="time-col">
                                {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00 - {{ str_pad($h+1, 2, '0', STR_PAD_LEFT) }}:00
                            </td>
                            
                            @foreach($days as $day)
                                @php
                                    $isWorking = false;
                                    $shiftName = '';
                                    $isStart = false;
                                    $isEnd = false;

                                    foreach($lichs as $lich) {
                                        if(\Carbon\Carbon::parse($lich->pivot->ngaylam)->format('Y-m-d') == $day->format('Y-m-d')) {
                                            $startH = (int) \Carbon\Carbon::parse($lich->giolam)->format('H');
                                            $endH = (int) \Carbon\Carbon::parse($lich->giotan)->format('H');
                                            
                                            if($h >= $startH && $h < $endH) {
                                                $isWorking = true;
                                                $shiftName = $lich->maca;
                                                
                                                if ($h == $startH) $isStart = true;       
                                                if ($h == $endH - 1) $isEnd = true;       
                                                break;
                                            }
                                        }
                                    }
                                @endphp

                                @if($isWorking)
                                    <td class="cell-active {{ $isStart ? 'cell-start' : '' }} {{ $isEnd ? 'cell-end' : '' }}">
                                        @if($isStart)
                                            <span class="shift-label">{{ $shiftName }}</span>
                                        @endif
                                    </td>
                                @else
                                    <td></td>
                                @endif

                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection