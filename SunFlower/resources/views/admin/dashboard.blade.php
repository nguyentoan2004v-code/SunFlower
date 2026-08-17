@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('page_title', ' Tổng quan kinh doanh')

@section('content')
<div class="sf-dashboard">

    {{-- ============================================================
         SECTION 0: GLOBAL FILTER BAR
         ============================================================ --}}
    <div class="sf-filter-bar mb-4">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-sliders" style="color: var(--sf-primary);"></i>
            <span class="fw-bold" style="font-size: 0.9rem;">Bộ lọc:</span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="sf-filter-btn active" data-filter="today">Hôm nay</button>
            <button class="sf-filter-btn" data-filter="week">Tuần này</button>
            <button class="sf-filter-btn" data-filter="month">Tháng này</button>
            <button class="sf-filter-btn" data-filter="quarter">Quý này</button>
            <button class="sf-filter-btn" data-filter="year">Năm nay</button>
            <button class="sf-filter-btn" data-filter="custom">
                <i class="fa-regular fa-calendar me-1"></i>Tùy chỉnh
            </button>
        </div>
    </div>

    {{-- ============================================================
         TABS NAVIGATION
         ============================================================ --}}
    <ul class="nav nav-pills mb-4 sf-main-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-revenue" data-bs-toggle="pill" data-bs-target="#content-revenue" type="button" role="tab" style="font-weight: 700; border-radius: 20px; padding: 8px 20px;">
                <i class="fa-solid fa-chart-pie me-1"></i> Tổng quan & Doanh thu
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-inventory" data-bs-toggle="pill" data-bs-target="#content-inventory" type="button" role="tab" style="font-weight: 700; border-radius: 20px; padding: 8px 20px; margin-left: 8px;">
                <i class="fa-solid fa-boxes-stacked me-1"></i> Sản phẩm & Tồn kho
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-customers" data-bs-toggle="pill" data-bs-target="#content-customers" type="button" role="tab" style="font-weight: 700; border-radius: 20px; padding: 8px 20px; margin-left: 8px;">
                <i class="fa-solid fa-users me-1"></i> Khách hàng & Hoạt động
            </button>
        </li>
    </ul>

    <div class="tab-content" id="dashboardTabsContent">
        {{-- ============================================================
             TAB 1: REVENUE & OVERVIEW
             ============================================================ --}}
        <div class="tab-pane fade show active" id="content-revenue" role="tabpanel" tabindex="0">

    {{-- ============================================================
         SECTION 0.5: AI STRATEGY CARD
         ============================================================ --}}
    <div class="sf-ai-card mb-4">
        <div class="d-flex align-items-start">
            <div class="me-3 flex-shrink-0">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, var(--sf-primary), var(--sf-pink)); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(255,140,66,0.3);">
                    <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 20px; color: #fff;"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start mb-2 flex-wrap gap-2">
                    <h6 class="fw-bold mb-0" style="font-size: 1.05rem;">
                        <i class="fa-solid fa-brain me-1" style="color: var(--sf-purple);"></i>
                        Gợi ý chiến lược kinh doanh
                    </h6>
                    <form method="POST" action="{{ route('admin.dashboard.refresh-ai') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm rounded-pill px-3" style="background: var(--sf-primary); color: #fff; font-size: 0.82rem; font-weight: 600;">
                            <i class="fa-solid fa-arrows-rotate me-1"></i> Làm mới
                        </button>
                    </form>
                </div>
                <p class="mb-2" style="font-size: 0.95rem; line-height: 1.7; color: var(--bs-body-color);">
                    {{ $aiAdvice ?? 'Đang tổng hợp dữ liệu kinh doanh...' }}
                </p>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="sf-badge sf-badge-primary"><i class="fa-solid fa-chart-pie me-1"></i>Dữ liệu 30 ngày</span>
                    <span class="sf-badge sf-badge-success"><i class="fa-regular fa-clock me-1"></i>Realtime</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 1: KPI CARDS (8 cards)
         ============================================================ --}}
    <div class="row g-3 mb-4">
        @php
            $kpis = [
                ['label' => 'Doanh thu hôm nay', 'value' => number_format($doanhThuNgay, 0, ',', '.') . 'đ', 'pct' => $pctDoanhThuNgay, 'icon' => 'fa-money-bill-trend-up', 'gradient' => 'linear-gradient(135deg, #FF8C42, #FFB088)', 'color' => '#FF8C42'],
                ['label' => 'Doanh thu tháng', 'value' => number_format($doanhThuThang, 0, ',', '.') . 'đ', 'pct' => $pctDoanhThuThang, 'icon' => 'fa-chart-line', 'gradient' => 'linear-gradient(135deg, #2ECC71, #82E0AA)', 'color' => '#2ECC71'],
                ['label' => 'Đơn hàng hôm nay', 'value' => $donHangNgay, 'pct' => $pctDonHangNgay, 'icon' => 'fa-shopping-bag', 'gradient' => 'linear-gradient(135deg, #3498DB, #85C1E9)', 'color' => '#3498DB'],
                ['label' => 'Đơn hàng tháng', 'value' => $donHangThang, 'pct' => $pctDonHangThang, 'icon' => 'fa-cart-shopping', 'gradient' => 'linear-gradient(135deg, #9B59B6, #C39BD3)', 'color' => '#9B59B6'],
                ['label' => 'Tổng khách hàng', 'value' => $tongKhachHang, 'pct' => $pctKhachHang, 'icon' => 'fa-users', 'gradient' => 'linear-gradient(135deg, #FDA4AF, #FDB5BD)', 'color' => '#E91E63'],
                ['label' => 'Khách mới tháng', 'value' => $khachHangMoi, 'pct' => $pctKhachHangMoi, 'icon' => 'fa-user-plus', 'gradient' => 'linear-gradient(135deg, #F1C40F, #F7DC6F)', 'color' => '#F1C40F'],
                ['label' => 'Tổng sản phẩm', 'value' => $tongSanPham, 'pct' => 0, 'icon' => 'fa-boxes-stacked', 'gradient' => 'linear-gradient(135deg, #1ABC9C, #76D7C4)', 'color' => '#1ABC9C'],
                ['label' => 'NL sắp hết hàng', 'value' => $spSapHetHang, 'pct' => 0, 'icon' => 'fa-triangle-exclamation', 'gradient' => 'linear-gradient(135deg, #E74C3C, #F1948A)', 'color' => '#E74C3C'],
            ];
        @endphp

        @foreach($kpis as $idx => $kpi)
        <div class="col-xl-3 col-lg-4 col-md-6 col-6">
            <div class="sf-kpi-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="sf-kpi-icon" style="background: {{ $kpi['gradient'] }}; color: #fff;">
                        <i class="fa-solid {{ $kpi['icon'] }}"></i>
                    </div>
                    @if($kpi['pct'] != 0)
                    <span class="sf-kpi-change {{ $kpi['pct'] > 0 ? 'up' : ($kpi['pct'] < 0 ? 'down' : 'neutral') }}">
                        <i class="fa-solid {{ $kpi['pct'] > 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="font-size: 0.65rem;"></i>
                        {{ abs($kpi['pct']) }}%
                    </span>
                    @endif
                </div>
                <div class="sf-kpi-label mt-2">{{ $kpi['label'] }}</div>
                <div class="sf-kpi-value">{{ $kpi['value'] }}</div>
                @if($idx < 4)
                <div class="sf-sparkline">
                    @php
                        $sparkData = $idx < 2 ? $sparkRevenue : $sparkOrders;
                        $max = max($sparkData) ?: 1;
                        $points = [];
                        foreach ($sparkData as $i => $v) {
                            $x = ($i / (count($sparkData) - 1)) * 100;
                            $y = 28 - (($v / $max) * 24);
                            $points[] = "$x,$y";
                        }
                        $polyline = implode(' ', $points);
                        $fillPoints = "0,28 " . $polyline . " 100,28";
                    @endphp
                    <svg viewBox="0 0 100 30" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="sparkGrad{{ $idx }}" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="{{ $kpi['color'] }}" stop-opacity="0.3"/>
                                <stop offset="100%" stop-color="{{ $kpi['color'] }}" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon points="{{ $fillPoints }}" fill="url(#sparkGrad{{ $idx }})" />
                        <polyline points="{{ $polyline }}" fill="none" stroke="{{ $kpi['color'] }}" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- ============================================================
         SECTION 2 & 3: REVENUE CHART + ORDER STATUS
         ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Revenue Line Chart --}}
        <div class="col-lg-12">
            <div class="sf-card sf-anim-card">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <div class="sf-card-title mb-0">
                        <i class="fa-solid fa-chart-line"></i> Biến động doanh thu
                    </div>
                    <div class="sf-chart-tabs">
                        <button class="sf-chart-tab active" data-period="7">7 ngày</button>
                        <button class="sf-chart-tab" data-period="30">30 ngày</button>
                        <button class="sf-chart-tab" data-period="12">12 tháng</button>
                    </div>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>


    </div>

    {{-- ============================================================
         SECTION: HIỆU SUẤT BÁN HÀNG
         ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-gauge-high me-2" style="color: var(--sf-primary);"></i>Hiệu suất bán hàng (Tháng này so với Tháng trước)</h6>
        </div>
        
        {{-- AOV --}}
        <div class="col-xl-3 col-md-6">
            <div class="sf-card sf-anim-card text-center" style="padding: 20px;">
                <div class="text-muted mb-2" style="font-size: 0.9rem;">Giá trị trung bình đơn (AOV)</div>
                <h4 class="fw-bold mb-2" style="color: var(--sf-primary);">{{ number_format($performanceData['aov'], 0, ',', '.') }}đ</h4>
                <div>
                    @if($performanceData['aov_trend'] > 0)
                        <span class="sf-badge sf-badge-success"><i class="fa-solid fa-arrow-trend-up me-1"></i>+{{ $performanceData['aov_trend'] }}%</span>
                    @elseif($performanceData['aov_trend'] < 0)
                        <span class="sf-badge sf-badge-danger"><i class="fa-solid fa-arrow-trend-down me-1"></i>{{ $performanceData['aov_trend'] }}%</span>
                    @else
                        <span class="sf-badge bg-secondary text-white"><i class="fa-solid fa-minus me-1"></i>0%</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Conversion Rate --}}
        <div class="col-xl-3 col-md-6">
            <div class="sf-card sf-anim-card text-center" style="padding: 20px;">
                <div class="text-muted mb-2" style="font-size: 0.9rem;">Tỷ lệ chuyển đổi</div>
                <h4 class="fw-bold mb-2" style="color: var(--sf-success);">{{ $performanceData['conversion'] }}%</h4>
                <div>
                    @if($performanceData['conversion_trend'] > 0)
                        <span class="sf-badge sf-badge-success"><i class="fa-solid fa-arrow-trend-up me-1"></i>+{{ $performanceData['conversion_trend'] }}%</span>
                    @elseif($performanceData['conversion_trend'] < 0)
                        <span class="sf-badge sf-badge-danger"><i class="fa-solid fa-arrow-trend-down me-1"></i>{{ $performanceData['conversion_trend'] }}%</span>
                    @else
                        <span class="sf-badge bg-secondary text-white"><i class="fa-solid fa-minus me-1"></i>0%</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cancel Rate --}}
        <div class="col-xl-3 col-md-6">
            <div class="sf-card sf-anim-card text-center" style="padding: 20px;">
                <div class="text-muted mb-2" style="font-size: 0.9rem;">Tỷ lệ hủy đơn</div>
                <h4 class="fw-bold mb-2" style="color: var(--sf-danger);">{{ $performanceData['cancel'] }}%</h4>
                <div>
                    @if($performanceData['cancel_trend'] > 0)
                        <span class="sf-badge sf-badge-danger" title="Tỷ lệ hủy tăng là dấu hiệu xấu"><i class="fa-solid fa-arrow-trend-up me-1"></i>+{{ $performanceData['cancel_trend'] }}%</span>
                    @elseif($performanceData['cancel_trend'] < 0)
                        <span class="sf-badge sf-badge-success" title="Tỷ lệ hủy giảm là dấu hiệu tốt"><i class="fa-solid fa-arrow-trend-down me-1"></i>{{ $performanceData['cancel_trend'] }}%</span>
                    @else
                        <span class="sf-badge bg-secondary text-white"><i class="fa-solid fa-minus me-1"></i>0%</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Return Customer Rate --}}
        <div class="col-xl-3 col-md-6">
            <div class="sf-card sf-anim-card text-center" style="padding: 20px;">
                <div class="text-muted mb-2" style="font-size: 0.9rem;">Tỷ lệ khách quay lại</div>
                <h4 class="fw-bold mb-2" style="color: var(--sf-info);">{{ $performanceData['returnRate'] }}%</h4>
                <div>
                    <span class="sf-badge sf-badge-info"><i class="fa-solid fa-users me-1"></i>Khách hàng thân thiết</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 8: REVENUE BY CATEGORY (Bar Chart)
         ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="sf-card sf-anim-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-chart-bar"></i> Doanh thu theo danh mục hoa
                </div>
                <div style="position: relative; height: 300px;">
                    <canvas id="categoryBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
        </div> {{-- End Tab 1 --}}

        {{-- ============================================================
             TAB 2: INVENTORY & PRODUCTS
             ============================================================ --}}
        <div class="tab-pane fade" id="content-inventory" role="tabpanel" tabindex="0">

    {{-- ============================================================
         SECTION: HIỆU SUẤT KHO HÀNG
         ============================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <h6 class="fw-bold mb-3"><i class="fa-solid fa-boxes-packing me-2" style="color: var(--sf-primary);"></i>Hiệu suất kho hàng (Tháng này so với Tháng trước)</h6>
        </div>
        
        {{-- Import Volume --}}
        <div class="col-xl-6 col-md-6">
            <div class="sf-card sf-anim-card text-center" style="padding: 20px;">
                <div class="text-muted mb-2" style="font-size: 0.9rem;">Khối lượng nhập kho</div>
                <h4 class="fw-bold mb-2" style="color: var(--sf-info);">{{ number_format($performanceData['import'], 0, ',', '.') }} đơn vị</h4>
                <div>
                    @if($performanceData['import_trend'] > 0)
                        <span class="sf-badge sf-badge-info"><i class="fa-solid fa-arrow-trend-up me-1"></i>+{{ $performanceData['import_trend'] }}% so với tháng trước</span>
                    @elseif($performanceData['import_trend'] < 0)
                        <span class="sf-badge sf-badge-warning"><i class="fa-solid fa-arrow-trend-down me-1"></i>{{ $performanceData['import_trend'] }}% so với tháng trước</span>
                    @else
                        <span class="sf-badge bg-secondary text-white" title="Không có chênh lệch hoặc chưa có dữ liệu tháng trước"><i class="fa-solid fa-minus me-1"></i>0% so với tháng trước</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Waste Rate --}}
        <div class="col-xl-6 col-md-6">
            <div class="sf-card sf-anim-card text-center" style="padding: 20px;">
                <div class="text-muted mb-2" style="font-size: 0.9rem;">Tỷ lệ hao hụt nguyên liệu (Hàng hỏng / Hàng xuất)</div>
                <h4 class="fw-bold mb-2" style="color: var(--sf-danger);">{{ $performanceData['waste'] }}%</h4>
                <div>
                    @if($performanceData['waste_trend'] > 0)
                        <span class="sf-badge sf-badge-danger" title="Tỷ lệ hao hụt tăng là dấu hiệu xấu"><i class="fa-solid fa-arrow-trend-up me-1"></i>+{{ $performanceData['waste_trend'] }}% so với tháng trước</span>
                    @elseif($performanceData['waste_trend'] < 0)
                        <span class="sf-badge sf-badge-success" title="Tỷ lệ hao hụt giảm là dấu hiệu tốt"><i class="fa-solid fa-arrow-trend-down me-1"></i>{{ $performanceData['waste_trend'] }}% so với tháng trước</span>
                    @else
                        <span class="sf-badge bg-secondary text-white" title="Không có chênh lệch hoặc chưa có dữ liệu tháng trước"><i class="fa-solid fa-minus me-1"></i>0% so với tháng trước</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 4 & 5: TOP PRODUCTS + INVENTORY
         ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Top 10 bán chạy --}}
        <div class="col-lg-7">
            <div class="sf-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-fire"></i> Top 10 sản phẩm bán chạy
                </div>
                <div class="sf-scrollable">
                    <table class="table sf-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Hình</th>
                                <th>Tên hoa</th>
                                <th>Danh mục</th>
                                <th class="text-center">Đã bán</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $idx => $sp)
                            <tr>
                                <td><span class="fw-bold" style="color: var(--sf-primary);">{{ $idx + 1 }}</span></td>
                                <td>
                                    @if($sp->hinhanh)
                                        <img src="{{ route('product.image', $sp->masp) }}" class="sf-product-thumb" alt="{{ $sp->tensp }}">
                                    @else
                                        <div class="sf-product-thumb d-flex align-items-center justify-content-center" style="background: var(--sf-primary-light);">
                                            <i class="fa-solid fa-seedling" style="color: var(--sf-primary); font-size: 0.8rem;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ Str::limit($sp->tensp, 25) }}</td>
                                <td><span class="sf-badge sf-badge-primary">{{ $sp->tendm ?? 'N/A' }}</span></td>
                                <td class="text-center fw-bold">{{ $sp->tong_ban }}</td>
                                <td class="text-end fw-bold" style="color: var(--sf-success);">{{ number_format($sp->doanh_thu, 0, ',', '.') }}đ</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Chưa có dữ liệu bán hàng</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Inventory Management --}}
        <div class="col-lg-5">
            <div class="sf-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-warehouse"></i> Quản lý kho hàng
                </div>

                {{-- Mini stats --}}
                <div class="row g-2 mb-3">
                    <div class="col-4">
                        <div class="sf-mini-stat">
                            <div class="sf-mini-val" style="color: var(--sf-info);">{{ $inventoryStats['total'] }}</div>
                            <div class="sf-mini-label">Nguyên liệu</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="sf-mini-stat">
                            <div class="sf-mini-val" style="color: var(--sf-success); font-size: 1rem;">{{ number_format($inventoryStats['giatri'], 0, ',', '.') }}đ</div>
                            <div class="sf-mini-label">Giá trị kho</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="sf-mini-stat">
                            <div class="sf-mini-val" style="color: var(--sf-warning);">{{ $inventoryStats['sapHetHan'] }}</div>
                            <div class="sf-mini-label">Sắp hết hạn</div>
                        </div>
                    </div>
                </div>

                {{-- Alert cards --}}
                <div class="d-flex flex-column gap-2 mb-3">
                    <div class="sf-inv-card {{ $inventoryStats['saphet'] == 0 ? 'sf-inv-green' : 'sf-inv-yellow' }}">
                        <i class="fa-solid fa-triangle-exclamation" style="color: {{ $inventoryStats['saphet'] == 0 ? 'var(--sf-success)' : 'var(--sf-warning)' }}; font-size: 1.1rem;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.88rem;">Sắp hết hàng</div>
                            <div style="font-size: 0.78rem; color: var(--sf-text-muted);">{{ $inventoryStats['saphet'] }} nguyên liệu dưới ngưỡng</div>
                        </div>
                        <span class="fw-bold" style="font-size: 1.2rem; color: {{ $inventoryStats['saphet'] == 0 ? 'var(--sf-success)' : 'var(--sf-warning)' }};">{{ $inventoryStats['saphet'] }}</span>
                    </div>
                    <div class="sf-inv-card {{ $inventoryStats['hetHang'] == 0 ? 'sf-inv-green' : 'sf-inv-red' }}">
                        <i class="fa-solid fa-times-circle" style="color: {{ $inventoryStats['hetHang'] == 0 ? 'var(--sf-success)' : 'var(--sf-danger)' }}; font-size: 1.1rem;"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold" style="font-size: 0.88rem;">Hết hàng</div>
                            <div style="font-size: 0.78rem; color: var(--sf-text-muted);">{{ $inventoryStats['hetHang'] }} nguyên liệu cần nhập</div>
                        </div>
                        <span class="fw-bold" style="font-size: 1.2rem; color: {{ $inventoryStats['hetHang'] == 0 ? 'var(--sf-success)' : 'var(--sf-danger)' }};">{{ $inventoryStats['hetHang'] }}</span>
                    </div>
                </div>

                {{-- Hao hụt --}}
                <div class="p-3 rounded-3" style="background: var(--sf-primary-light);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="font-size: 0.88rem;"><i class="fa-solid fa-leaf me-1" style="color: var(--sf-primary);"></i>Hao hụt 30 ngày</span>
                        <span class="sf-badge {{ $tyLeHaoHut > 10 ? 'sf-badge-danger' : 'sf-badge-success' }}">{{ $tyLeHaoHut }}%</span>
                    </div>
                    <div class="sf-progress">
                        <div class="sf-progress-bar" style="width: {{ min($tyLeHaoHut, 100) }}%; background: {{ $tyLeHaoHut > 10 ? 'var(--sf-danger)' : 'var(--sf-success)' }};"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-1" style="font-size: 0.78rem; color: var(--sf-text-muted);">
                        <span>Đã bán: <b style="color: var(--sf-info);">{{ $tongBan30Ngay }}</b></span>
                        <span>Đã hủy: <b style="color: var(--sf-danger);">{{ $tongHuy30Ngay }}</b></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 4b: TOP TỒN KHO + SẮP HẾT HÀNG
         ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Top tồn kho cao --}}
        <div class="col-lg-6">
            <div class="sf-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-cubes-stacked"></i> Top 10 tồn kho cao
                </div>
                <div class="sf-scrollable">
                    <table class="table sf-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên nguyên liệu</th>
                                <th class="text-center">Tồn kho</th>
                                <th class="text-end">Giá trị tồn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topStockProducts as $idx => $nl)
                            <tr>
                                <td><span class="fw-bold" style="color: var(--sf-info);">{{ $idx + 1 }}</span></td>
                                <td class="fw-semibold">{{ $nl->ten_nl }}</td>
                                <td class="text-center"><span class="sf-badge sf-badge-info">{{ $nl->tonkho_thucte }} {{ $nl->dvt }}</span></td>
                                <td class="text-end fw-bold">{{ number_format($nl->gia_tri_ton, 0, ',', '.') }}đ</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sắp hết hàng --}}
        <div class="col-lg-6">
            <div class="sf-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-exclamation-triangle"></i> Nguyên liệu sắp hết hàng
                </div>
                <div class="sf-scrollable">
                    <table class="table sf-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tên nguyên liệu</th>
                                <th class="text-center">Tồn kho</th>
                                <th class="text-center">Ngưỡng</th>
                                <th class="text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowStockProducts as $idx => $nl)
                            <tr>
                                <td><span class="fw-bold" style="color: var(--sf-danger);">{{ $idx + 1 }}</span></td>
                                <td class="fw-semibold">{{ $nl->ten_nl }}</td>
                                <td class="text-center"><span class="fw-bold" style="color: var(--sf-danger);">{{ $nl->tonkho_thucte }} {{ $nl->dvt }}</span></td>
                                <td class="text-center">{{ $nl->tonkho_toithieu }} {{ $nl->dvt }}</td>
                                <td class="text-center">
                                    @if($nl->tonkho_thucte <= $nl->tonkho_toithieu * 0.3)
                                        <span class="sf-badge sf-badge-danger">Nguy hiểm</span>
                                    @else
                                        <span class="sf-badge sf-badge-warning">Cận ngưỡng</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-4" style="color: var(--sf-success);"><i class="fa-solid fa-check-circle me-1"></i>Kho hàng an toàn</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
        </div> {{-- End Tab 2 --}}

        {{-- ============================================================
             TAB 3: CUSTOMERS & ACTIVITY
             ============================================================ --}}
        <div class="tab-pane fade" id="content-customers" role="tabpanel" tabindex="0">

    {{-- ============================================================
         SECTION 9: PERFORMANCE + SECTION 6: CUSTOMERS
         ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Performance --}}
        <div class="col-lg-5">
            <div class="sf-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-gauge-high"></i> Hiệu suất bán hàng
                </div>

                {{-- AOV --}}
                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-3" style="background: var(--sf-primary-light);">
                    <div>
                        <div style="font-size: 0.78rem; font-weight: 600; color: var(--sf-text-muted); text-transform: uppercase;">Giá trị đơn TB (AOV)</div>
                        <div style="font-size: 1.5rem; font-weight: 800; color: var(--sf-primary);">{{ number_format($performanceData['aov'], 0, ',', '.') }}đ</div>
                    </div>
                    <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, var(--sf-primary), var(--sf-pink)); display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-coins" style="color: #fff; font-size: 1.1rem;"></i>
                    </div>
                </div>

                {{-- Progress bars --}}
                <div class="sf-progress-wrap">
                    <div class="sf-progress-label">
                        <span><i class="fa-solid fa-check-circle me-1" style="color: var(--sf-success);"></i>Tỷ lệ hoàn thành</span>
                        <span style="color: var(--sf-success);">{{ $performanceData['conversion'] }}%</span>
                    </div>
                    <div class="sf-progress">
                        <div class="sf-progress-bar" style="width: {{ $performanceData['conversion'] }}%; background: linear-gradient(90deg, var(--sf-success), #82E0AA);"></div>
                    </div>
                </div>
                <div class="sf-progress-wrap">
                    <div class="sf-progress-label">
                        <span><i class="fa-solid fa-ban me-1" style="color: var(--sf-danger);"></i>Tỷ lệ hủy đơn</span>
                        <span style="color: var(--sf-danger);">{{ $performanceData['cancel'] }}%</span>
                    </div>
                    <div class="sf-progress">
                        <div class="sf-progress-bar" style="width: {{ $performanceData['cancel'] }}%; background: linear-gradient(90deg, var(--sf-danger), #F1948A);"></div>
                    </div>
                </div>
                <div class="sf-progress-wrap">
                    <div class="sf-progress-label">
                        <span><i class="fa-solid fa-rotate-left me-1" style="color: var(--sf-purple);"></i>Khách quay lại</span>
                        <span style="color: var(--sf-purple);">{{ $performanceData['returnRate'] }}%</span>
                    </div>
                    <div class="sf-progress">
                        <div class="sf-progress-bar" style="width: {{ $performanceData['returnRate'] }}%; background: linear-gradient(90deg, var(--sf-purple), #C39BD3);"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Customer Stats --}}
        <div class="col-lg-7">
            <div class="sf-card">
                <div class="sf-card-title">
                    <i class="fa-solid fa-users"></i> Thống kê khách hàng
                </div>

                {{-- Mini KPIs --}}
                <div class="row g-2 mb-3">
                    @php
                        $custKpis = [
                            ['label' => 'Tổng KH', 'val' => $customerStats['total'], 'color' => 'var(--sf-info)', 'icon' => 'fa-users'],
                            ['label' => 'KH mới', 'val' => $customerStats['moi'], 'color' => 'var(--sf-success)', 'icon' => 'fa-user-plus'],
                            ['label' => 'Quay lại', 'val' => $customerStats['quaylai'], 'color' => 'var(--sf-purple)', 'icon' => 'fa-rotate-left'],
                            ['label' => 'VIP', 'val' => $customerStats['vip'], 'color' => 'var(--sf-primary)', 'icon' => 'fa-crown'],
                        ];
                    @endphp
                    @foreach($custKpis as $ck)
                    <div class="col-3">
                        <div class="sf-mini-stat">
                            <i class="fa-solid {{ $ck['icon'] }} mb-1" style="color: {{ $ck['color'] }}; font-size: 1rem;"></i>
                            <div class="sf-mini-val" style="color: {{ $ck['color'] }};">{{ $ck['val'] }}</div>
                            <div class="sf-mini-label">{{ $ck['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Top customers table --}}
                <table class="table sf-table mb-0">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th class="text-center">Số đơn</th>
                            <th class="text-end">Tổng chi tiêu</th>
                            <th class="text-center">Hạng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomers as $kh)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($kh->hoten) }}&background=FF8C42&color=fff&size=36" class="sf-avatar" alt="">
                                    <div>
                                        <div class="fw-bold" style="font-size: 0.88rem;">{{ $kh->hoten }}</div>
                                        <div style="font-size: 0.75rem; color: var(--sf-text-muted);">{{ $kh->email ?? $kh->sdt }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-bold">{{ $kh->so_don ?? 0 }}</td>
                            <td class="text-end fw-bold" style="color: var(--sf-success);">{{ number_format($kh->tong_chi_tieu ?? 0, 0, ',', '.') }}đ</td>
                            <td class="text-center">
                                @if($kh->hangThanhVien)
                                    <span class="sf-badge sf-badge-purple">{{ $kh->hangThanhVien->ten_hang }}</span>
                                @else
                                    <span class="sf-badge" style="background: rgba(0,0,0,0.05); color: var(--sf-text-muted);">Thường</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-4">Chưa có khách hàng</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SECTION 7: RECENT ORDERS + SECTION 10 & 11: NOTIFICATIONS + TIMELINE
         ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Recent Orders --}}
        <div class="col-lg-7">
            <div class="sf-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="sf-card-title mb-0">
                        <i class="fa-solid fa-clock-rotate-left"></i> Đơn hàng mới nhất
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm rounded-pill px-3" style="background: var(--sf-primary-light); color: var(--sf-primary); font-weight: 600; font-size: 0.82rem;">
                        Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="sf-scrollable">
                    <table class="table sf-table mb-0">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th class="text-end">Giá trị</th>
                                <th class="text-center">Trạng thái</th>
                                <th>Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->madon) }}" class="fw-bold text-decoration-none" style="color: var(--sf-primary);">#{{ $order->madon }}</a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($order->khachhang->hoten ?? 'K') }}&background=FF8C42&color=fff&size=28" style="width: 28px; height: 28px; border-radius: 50%;" alt="">
                                        <span class="fw-semibold">{{ Str::limit($order->khachhang->hoten ?? 'Khách vãng lai', 18) }}</span>
                                    </div>
                                </td>
                                <td class="text-end fw-bold">{{ number_format($order->tongtien, 0, ',', '.') }}đ</td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($order->trangthai) {
                                            'Đã hoàn thành' => 'sf-badge-success',
                                            'Chờ xác nhận' => 'sf-badge-warning',
                                            'Đã xác nhận' => 'sf-badge-info',
                                            'Đang giao hàng' => 'sf-badge-primary',
                                            'Đã hủy' => 'sf-badge-danger',
                                            default => 'sf-badge-info',
                                        };
                                    @endphp
                                    <span class="sf-badge {{ $badgeClass }}">{{ $order->trangthai }}</span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--sf-text-muted);">
                                    {{ \Carbon\Carbon::parse($order->ngaydat)->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Không có đơn hàng</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Notifications + Timeline --}}
        <div class="col-lg-5">

            {{-- Activity Timeline --}}
            <div class="sf-card" style="height: auto;">
                <div class="sf-card-title">
                    <i class="fa-solid fa-timeline"></i> Lịch hoạt động
                </div>
                <div class="sf-timeline sf-scrollable" style="max-height: 300px;">
                    @forelse($activityTimeline as $event)
                    @php
                        $tlColorMap = [
                            'primary' => '#FF8C42', 'success' => '#2ECC71', 'danger' => '#E74C3C',
                            'info' => '#3498DB', 'warning' => '#F1C40F', 'secondary' => '#95A5A6',
                        ];
                        $tlColor = $tlColorMap[$event['color']] ?? '#FF8C42';
                    @endphp
                    <div class="sf-timeline-item">
                        <div class="sf-timeline-dot" style="background: {{ $tlColor }};"></div>
                        <div class="sf-timeline-title">
                            <i class="fa-solid {{ $event['icon'] }} me-1" style="color: {{ $tlColor }}; font-size: 0.8rem;"></i>
                            {{ $event['title'] }}
                        </div>
                        <div class="sf-timeline-desc">{{ $event['desc'] }}</div>
                        <div class="sf-timeline-time">
                            <i class="fa-regular fa-clock me-1"></i>{{ $event['time']->diffForHumans() }}
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3" style="color: var(--sf-text-muted);">Chưa có hoạt động</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
        </div> {{-- End Tab 3 --}}
    </div> {{-- End Tab Content --}}

</div>
{{-- END sf-dashboard --}}

{{-- ============================================================
     CHART.JS + SCRIPTS
     ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const textColor = isDark ? '#adb5bd' : '#6c757d';
    const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.04)';

    // ============================================================
    // Revenue Chart Data (multi-period)
    // ============================================================
    const revenueDataSets = {
        '7': { labels: @json($revenueLabels7), data: @json($revenueData7) },
        '30': { labels: @json($revenueLabels30), data: @json($revenueData30) },
        '12': { labels: @json($revenueLabels12), data: @json($revenueData12) },
    };

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revenueCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(255, 140, 66, 0.25)');
    gradient.addColorStop(1, 'rgba(255, 140, 66, 0)');

    const revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueDataSets['7'].labels,
            datasets: [{
                label: 'Doanh thu',
                data: revenueDataSets['7'].data,
                borderColor: '#FF8C42',
                backgroundColor: gradient,
                borderWidth: 2.5,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#FF8C42',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#FF8C42',
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#2c3034' : '#fff',
                    titleColor: isDark ? '#fff' : '#2D3436',
                    bodyColor: isDark ? '#adb5bd' : '#6c757d',
                    borderColor: '#FF8C42',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 12,
                    callbacks: {
                        label: function(ctx) {
                            return new Intl.NumberFormat('vi-VN').format(ctx.raw) + 'đ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: gridColor, drawBorder: false },
                    ticks: {
                        color: textColor,
                        font: { size: 11, weight: '600' },
                        callback: function(v) {
                            if (v >= 1000000) return (v/1000000).toFixed(1) + 'M';
                            if (v >= 1000) return (v/1000) + 'K';
                            return v;
                        }
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 11, weight: '600' } }
                }
            }
        }
    });

    // Tab switching
    document.querySelectorAll('.sf-chart-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.sf-chart-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const period = this.dataset.period;
            const ds = revenueDataSets[period];
            revenueChart.data.labels = ds.labels;
            revenueChart.data.datasets[0].data = ds.data;
            revenueChart.update('active');
        });
    });


    // ============================================================
    // Category Bar Chart
    // ============================================================
    const catCtx = document.getElementById('categoryBarChart').getContext('2d');
    const catGradient = catCtx.createLinearGradient(0, 0, 0, 280);
    catGradient.addColorStop(0, '#FF8C42');
    catGradient.addColorStop(1, '#FFB088');

    new Chart(catCtx, {
        type: 'bar',
        data: {
            labels: @json($catBarLabels),
            datasets: [{
                label: 'Doanh thu',
                data: @json($catBarRevenue),
                backgroundColor: catGradient,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 36,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: isDark ? '#2c3034' : '#fff',
                    titleColor: isDark ? '#fff' : '#2D3436',
                    bodyColor: isDark ? '#adb5bd' : '#6c757d',
                    borderColor: '#FF8C42',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 12,
                    callbacks: {
                        label: function(ctx) {
                            const qty = @json($catBarQty);
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(ctx.raw) + 'đ | Đã bán: ' + (qty[ctx.dataIndex] || 0);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: gridColor, drawBorder: false },
                    ticks: {
                        color: textColor,
                        font: { size: 11, weight: '600' },
                        callback: function(v) {
                            if (v >= 1000000) return (v/1000000).toFixed(1) + 'M';
                            if (v >= 1000) return (v/1000) + 'K';
                            return v;
                        }
                    }
                },
                y: {
                    grid: { display: false },
                    ticks: { color: textColor, font: { size: 12, weight: '600' } }
                }
            }
        }
    });

    // ============================================================
    // Filter bar (UI only - visual feedback)
    // ============================================================
    document.querySelectorAll('.sf-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.sf-filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endsection