@extends('layouts.admin')

@section('title', 'Bảng điều khiển chiến lược')
@section('page_title', 'Tổng quan hiệu suất kinh doanh')

@section('content')
<div class="container-fluid px-0">

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #ffffff; overflow: hidden;">
                <div style="height: 4px; background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);"></div>
                <div class="card-body p-4 d-flex align-items-start">
                    <div class="me-4 flex-shrink-0">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(142, 197, 252, 0.4);">
                            <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 22px; color: #4318ff;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0" style="color: #2b3674; font-size: 1.15rem;">
                                Gợi ý chiến lược định hướng kinh doanh
                            </h5>
                            <form method="POST" action="{{ route('admin.dashboard.refresh-ai') }}">
                                @csrf
                                <button type="submit" class="btn btn-sm bg-white border border-primary-subtle text-primary rounded-pill px-3 shadow-sm">
                                    <i class="fa-solid fa-arrows-rotate me-1"></i> Cập nhật
                                </button>
                            </form>
                        </div>
                        
                        <p class="mb-3" style="font-size: 1.05rem; line-height: 1.6; color: #4a5568;">
                            {{ $aiAdvice ?? 'Hệ thống đang kết nối máy chủ để tổng hợp dữ liệu kinh doanh...' }}
                        </p>
                        
                        <div class="d-flex align-items-center mt-1">
                            <span class="badge rounded-pill bg-light text-primary border border-primary-subtle px-3 py-2" style="font-weight: 500; font-size: 0.85rem;">
                                <i class="fa-solid fa-chart-pie me-1"></i> Dữ liệu chuỗi hóa đơn 30 ngày
                            </span>
                            <span class="badge rounded-pill bg-light text-success border border-success-subtle px-3 py-2 ms-2" style="font-weight: 500; font-size: 0.85rem;">
                                <i class="fa-regular fa-clock me-1"></i> Cập nhật thời gian thực
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-dark" style="border-radius: 12px; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.9rem;">ĐƠN HÀNG MỚI</h6>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;">{{ $donHangMoiCount }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; background-color: #fff9db; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-shopping-bag text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-dark" style="border-radius: 12px; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.9rem;">DOANH THU HÔM NAY</h6>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;">{{ number_format($doanhThuNgay, 0, ',', '.') }}đ</h3>
                    </div>
                    <div style="width: 48px; height: 48px; background-color: #e6fffa; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-money-bill-trend-up text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-dark" style="border-radius: 12px; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.9rem;">DANH MỤC SẢN PHẨM</h6>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;">{{ $tongSanPham }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; background-color: #e8f2ff; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-boxes-stacked text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-4 text-dark" style="border-radius: 12px; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-semibold mb-1" style="font-size: 0.9rem;">NHÂN LỰC HỆ THỐNG</h6>
                        <h3 class="fw-bold mb-0" style="color: #2b3674;">{{ $tongNhanVien }}</h3>
                    </div>
                    <div style="width: 48px; height: 48px; background-color: #ffe3e3; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-users text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #2b3674; font-size: 1.1rem;">Biến Động Doanh Thu Giao Dịch</h5>
                    <span class="text-muted style" style="font-size: 0.85rem;">7 ngày gần nhất</span>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 12px; background-color: #ffffff;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color: #2b3674; font-size: 1.1rem;">Cơ Cấu Sản Phẩm Danh Mục</h5>
                </div>
                <div style="position: relative; height: 320px; display: flex; align-items: center; justify-content: center;">
                    <canvas id="categoryDistributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px; background-color: #ffffff;">
                <h5 class="fw-bold mb-4" style="color: #2b3674; font-size: 1.1rem;">Nhật ký đơn hàng vừa phát sinh</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr style="color: #a3aed0; font-size: 0.85rem; letter-spacing: 0.5px;">
                                <th class="border-0">MÃ ĐƠN</th>
                                <th class="border-0">KHÁCH HÀNG</th>
                                <th class="border-0">THỜI ĐIỂM ĐẶT</th>
                                <th class="border-0">TRẠNG THÁI CHUYỂN</th>
                                <th class="border-0 text-end">GIÁ TRỊ</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 0.95rem; color: #2b3674; font-weight: 500;">
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="text-primary fw-bold">#{{ $order->madon }}</td>
                                <td>{{ $order->khachhang->hoten ?? 'Khách vãng lai' }}</td>
                                <td class="text-muted">{{ $order->ngaydat }}</td>
                                <td>
                                    @if($order->trangthai == 'Chờ xác nhận')
                                        <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill" style="font-size: 0.8rem;">Chờ xử lý</span>
                                    @elseif($order->trangthai == 'Đã hoàn thành')
                                        <span class="badge bg-success px-3 py-1.5 rounded-pill" style="font-size: 0.8rem;">Hoàn thành</span>
                                    @else
                                        <span class="badge bg-secondary px-3 py-1.5 rounded-pill" style="font-size: 0.8rem;">{{ $order->trangthai }}</span>
                                    @endif
                                </td>
                                <td class="text-end fw-bold">{{ number_format($order->tongtien, 0, ',', '.') }}đ</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Không có đơn hàng nào gần đây.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px; background-color: #ffffff;">
                <h5 class="fw-bold mb-3" style="color: #2b3674; font-size: 1.1rem;">Cảnh báo chỉ số kho bãi</h5>
                
                <div class="d-flex flex-column gap-3 mt-2">
                    @if(isset($lowStockProducts) && $lowStockProducts->count() > 0)
                        @foreach($lowStockProducts as $sp)
                        <div class="p-3 rounded-3 d-flex align-items-center" style="background-color: #fff5f5; border: 1px solid #ffe3e3;">
                            <i class="fa-solid fa-triangle-exclamation text-danger fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold text-danger" style="font-size: 0.9rem; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $sp->tensp }}</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.8rem;">Cảnh báo tồn thấp: Chỉ còn <b class="text-danger">{{ $sp->soluong }}</b> sản phẩm.</p>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="p-3 rounded-3 d-flex align-items-center" style="background-color: #f0fdf4; border: 1px solid #dcfce7;">
                            <i class="fa-solid fa-circle-check text-success fs-4 me-3"></i>
                            <div>
                                <h6 class="mb-1 fw-bold text-success" style="font-size: 0.9rem;">Vòng quay kho đạt chuẩn</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.8rem;">Tất cả sản phẩm hiện đang ở mức lưu kho an toàn.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Nhận dữ liệu mảng động từ Controller thông qua cú pháp JSON của Blade
    const chartLabels = @json($revenueLabels ?? []);
    const chartData = @json($revenueData ?? []);
    
    const categoryLabels = @json($catLabels ?? []);
    const categoryData = @json($catData ?? []);

    // 1. Cấu hình đồ thị đường - Biến động doanh thu giao dịch
    const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
    const revenueGradient = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    revenueGradient.addColorStop(0, 'rgba(79, 172, 254, 0.4)');
    revenueGradient.addColorStop(1, 'rgba(79, 172, 254, 0.0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Doanh thu ngày (đ)',
                data: chartData,
                borderColor: '#4facfe',
                borderWidth: 3,
                backgroundColor: revenueGradient,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#4facfe',
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: { color: '#f3f4f6' },
                    ticks: { color: '#a3aed0', font: { family: 'sans-serif' } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#a3aed0', font: { family: 'sans-serif' } }
                }
            }
        }
    });

    // 2. Cấu hình đồ thị tròn - Cơ cấu sản phẩm theo từng nhóm danh mục
    const ctxCategory = document.getElementById('categoryDistributionChart').getContext('2d');
    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: ['#4facfe', '#00f2fe', '#ffd83b', '#ff6b6b', '#28a745', '#7000ff', '#ff9f43'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 12,
                        color: '#4a5568',
                        font: { weight: '500', size: 11 }
                    }
                }
            },
            cutout: '70%'
        }
    });
</script>

<script>
    // Hàm xử lý tương tác hiệu ứng xoay và tải lại dữ liệu của nút Cập nhật
    function reloadDashboard(btn) {
        const icon = document.getElementById('refresh-icon');
        if(icon) {
            icon.classList.add('fa-spin');
        }
        
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải...';
        btn.classList.add('disabled');
        btn.style.opacity = '0.7';

        setTimeout(() => {
            window.location.reload();
        }, 400);
    }
</script>
@endsection