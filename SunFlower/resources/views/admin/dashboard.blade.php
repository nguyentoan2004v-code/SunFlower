@extends('layouts.admin')

@section('title', 'Bảng điều khiển chiến lược')
@section('page_title', 'Tổng quan hiệu suất kinh doanh')

@section('content')
<div class="container-fluid px-0">

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-body-tertiary" style="border-radius: 12px; overflow: hidden;">
                <div style="height: 4px; background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);"></div>
                <div class="card-body p-4 d-flex align-items-start">
                    <div class="me-4 flex-shrink-0">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(142, 197, 252, 0.4);">
                            <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 22px; color: #4318ff;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0 text-body" style="font-size: 1.15rem;">
                                Gợi ý chiến lược định hướng kinh doanh
                            </h5>
                            <form method="POST" action="{{ route('admin.dashboard.refresh-ai') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm">
                                    <i class="fa-solid fa-arrows-rotate me-1"></i> Cập nhật
                                </button>
                            </form>
                        </div>
                        
                        <p class="mb-3 text-body-secondary" style="font-size: 1.05rem; line-height: 1.6;">
                            {{ $aiAdvice ?? 'Hệ thống đang kết nối máy chủ để tổng hợp dữ liệu kinh doanh...' }}
                        </p>
                        
                        <div class="d-flex align-items-center mt-1">
                            <span class="badge rounded-pill bg-body-secondary text-primary border px-3 py-2" style="font-weight: 500; font-size: 0.85rem;">
                                <i class="fa-solid fa-chart-pie me-1"></i> Dữ liệu chuỗi hóa đơn 30 ngày
                            </span>
                            <span class="badge rounded-pill bg-body-secondary text-success border px-3 py-2 ms-2" style="font-weight: 500; font-size: 0.85rem;">
                                <i class="fa-regular fa-clock me-1"></i> Cập nhật thời gian thực
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        @foreach([['title' => 'ĐƠN HÀNG MỚI', 'val' => $donHangMoiCount, 'icon' => 'fa-shopping-bag', 'col' => 'text-warning', 'bg' => '#fff9db'], ['title' => 'DOANH THU HÔM NAY', 'val' => number_format($doanhThuNgay, 0, ',', '.') . 'đ', 'icon' => 'fa-money-bill-trend-up', 'col' => 'text-success', 'bg' => '#e6fffa'], ['title' => 'DANH MỤC SẢN PHẨM', 'val' => $tongSanPham, 'icon' => 'fa-boxes-stacked', 'col' => 'text-primary', 'bg' => '#e8f2ff'], ['title' => 'NHÂN LỰC HỆ THỐNG', 'val' => $tongNhanVien, 'icon' => 'fa-users', 'col' => 'text-danger', 'bg' => '#ffe3e3']] as $s)
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 bg-body-tertiary" style="border-radius: 12px;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-body-secondary fw-semibold mb-1" style="font-size: 0.9rem;">{{ $s['title'] }}</h6>
                        <h3 class="fw-bold mb-0 text-body">{{ $s['val'] }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; background-color: {{ $s['bg'] }}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid {{ $s['icon'] }} {{ $s['col'] }} fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 bg-body-tertiary" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-body" style="font-size: 1.1rem;">Biến Động Doanh Thu Giao Dịch</h5>
                <div style="position: relative; height: 320px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-body-tertiary" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-body" style="font-size: 1.1rem;">Cơ Cấu Sản Phẩm Danh Mục</h5>
                <div style="position: relative; height: 320px;">
                    <canvas id="categoryDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4 bg-body-tertiary" style="border-radius: 12px;">
                <h5 class="fw-bold mb-4 text-body" style="font-size: 1.1rem;">Nhật ký đơn hàng vừa phát sinh</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-body-secondary" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                                <th class="border-0">MÃ ĐƠN</th>
                                <th class="border-0">KHÁCH HÀNG</th>
                                <th class="border-0">THỜI ĐIỂM ĐẶT</th>
                                <th class="border-0">TRẠNG THÁI</th>
                                <th class="border-0 text-end">GIÁ TRỊ</th>
                            </tr>
                        </thead>
                        <tbody class="text-body" style="font-size: 0.95rem; font-weight: 500;">
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="text-primary fw-bold">#{{ $order->madon }}</td>
                                <td>{{ $order->khachhang->hoten ?? 'Khách vãng lai' }}</td>
                                <td class="text-body-secondary">{{ $order->ngaydat }}</td>
                                <td><span class="badge {{ $order->trangthai == 'Đã hoàn thành' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill">{{ $order->trangthai }}</span></td>
                                <td class="text-end fw-bold">{{ number_format($order->tongtien, 0, ',', '.') }}đ</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-body-secondary">Không có đơn hàng.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 mb-4 bg-body-tertiary" style="border-radius: 12px;">
                <h5 class="fw-bold mb-3 text-body" style="font-size: 1.1rem;">Cảnh báo chỉ số kho bãi</h5>
                @forelse($lowStockProducts as $sp)
                <div class="p-3 rounded-3 border mb-2" style="background-color: var(--bs-tertiary-bg);">
                    <h6 class="mb-1 fw-bold text-danger">{{ $sp->tensp }}</h6>
                    <p class="mb-0 text-body-secondary" style="font-size: 0.8rem;">Tồn thấp: {{ $sp->soluong }}</p>
                </div>
                @empty
                <div class="text-success border border-success p-3 rounded">Kho an toàn</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Logic: Màu chữ biểu đồ tự đổi theo theme
    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
    const textColor = isDark ? '#adb5bd' : '#a3aed0';

    new Chart(document.getElementById('revenueTrendChart').getContext('2d'), {
        type: 'line',
        data: { labels: @json($revenueLabels ?? []), datasets: [{ data: @json($revenueData ?? []), borderColor: '#4facfe', fill: true, tension: 0.35 }] },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { ticks: { color: textColor } }, x: { ticks: { color: textColor } } } }
    });

    new Chart(document.getElementById('categoryDistributionChart').getContext('2d'), {
        type: 'doughnut',
        data: { labels: @json($catLabels ?? []), datasets: [{ data: @json($catData ?? []), backgroundColor: ['#4facfe', '#00f2fe', '#ffd83b', '#ff6b6b'] }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { color: textColor } } } }
    });
</script>
@endsection