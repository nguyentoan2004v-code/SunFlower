<!DOCTYPE html>
<html lang="vi" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')  SunFlower Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: var(--bs-body-bg); transition: background-color 0.3s, color 0.3s; }
        :root { --sunflower-orange: #FF8C00; --admin-sidebar: #2c3e50; }
        
        /* Chỉnh sửa Sidebar để tương thích cả 2 chế độ */
        .sidebar {
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            background: var(--admin-sidebar);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        /* Ẩn thanh cuộn cho sidebar nhưng vẫn giữ chức năng cuộn */
        .sidebar::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }
        .sidebar {
            -ms-overflow-style: none; /* IE và Edge */
            scrollbar-width: none; /* Firefox */
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-radius: 5px;
            margin: 5px 15px;
            transition: all 0.2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sunflower-orange);
            color: white;
        }
        .sidebar .nav-link i { width: 25px; }
        
        /* Topbar linh hoạt theo theme */
        .topbar {
            background: var(--bs-body-bg);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            transition: background-color 0.3s;
        }
        
        .content-wrapper { padding: 30px; }
        .btn-sun { background: var(--sunflower-orange); color: white; border: none; }
        .btn-sun:hover { background: #e67e00; color: white; }
        
        /* Card tùy biến */
        .card-custom { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            background-color: var(--bs-body-bg);
        }

        /* --- TÙY CHỈNH RIÊNG KHI BẬT DARK MODE --- */
        [data-bs-theme="dark"] body {
            background-color: #1a1d20;
        }
        [data-bs-theme="dark"] .sidebar {
            background: #121416; /* Sidebar tối hơn một chút */
            border-right: 1px solid #2b3035;
        }
        [data-bs-theme="dark"] .topbar {
            border-bottom: 1px solid #2b3035;
            box-shadow: none;
        }
        [data-bs-theme="dark"] .card-custom {
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            border: 1px solid #2b3035;
        }
        /* Nút toggle Dark Mode */
        .theme-toggle-btn {
            background: transparent;
            border: none;
            color: var(--bs-body-color);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        .theme-toggle-btn:hover {
            background: rgba(128, 128, 128, 0.1);
        }

        /* Phân trang (Pagination) */
        nav p,
        nav .small.text-muted,
        .card-footer p {
            display: none !important;
        }
        nav .d-none.flex-sm-fill > div:first-child {
            display: none !important;
        }
        nav.d-flex,
        nav .justify-content-between,
        nav .justify-content-sm-between {
            justify-content: center !important;
        }
        .pagination {
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .pagination .page-item .page-link {
            color: #495057;
            border-radius: 8px !important;
            border: 1px solid #dee2e6;
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: var(--sunflower-orange) !important;
            border-color: var(--sunflower-orange) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(255, 140, 0, 0.3);
        }
        .pagination .page-item .page-link:hover {
            background-color: #fff3e6;
            color: var(--sunflower-orange);
            border-color: var(--sunflower-orange);
        }
        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background-color: #f8f9fa;
            border-color: #e9ecef;
            cursor: not-allowed;
        }
        [data-bs-theme="dark"] .pagination .page-item .page-link {
            background-color: #212529;
            border-color: #373b3e;
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .pagination .page-item.disabled .page-link {
            background-color: #1a1d20;
            border-color: #2b3035;
            color: #495057;
        }
        [data-bs-theme="dark"] .pagination .page-item.active .page-link {
            background-color: var(--sunflower-orange) !important;
            border-color: var(--sunflower-orange) !important;
            color: #ffffff !important;
        }
        [data-bs-theme="dark"] .pagination .page-item .page-link:hover {
            background-color: #2c3034;
            color: #ffffff;
        }

        /* ============================================================
           SUNFLOWER DASHBOARD DESIGN SYSTEM
           ============================================================ */
        :root {
            --sf-primary: #FF8C42;
            --sf-primary-light: #FFF3E8;
            --sf-primary-dark: #E07830;
            --sf-success: #2ECC71;
            --sf-danger: #E74C3C;
            --sf-warning: #F1C40F;
            --sf-info: #3498DB;
            --sf-purple: #9B59B6;
            --sf-pink: #FDA4AF;
            --sf-bg-warm: #FBF7F4;
            --sf-text: #2D3436;
            --sf-text-muted: #95A5A6;
            --sf-radius: 16px;
            --sf-radius-sm: 12px;
            --sf-shadow: 0 2px 20px rgba(255,140,66,0.08);
            --sf-shadow-hover: 0 8px 30px rgba(255,140,66,0.15);
            --sf-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        [data-bs-theme="dark"] {
            --sf-primary-light: #3d2a1a;
            --sf-bg-warm: #1a1d20;
            --sf-shadow: 0 2px 20px rgba(0,0,0,0.2);
            --sf-shadow-hover: 0 8px 30px rgba(0,0,0,0.3);
        }

        /* --- Main Tabs Navigation --- */
        .sf-main-tabs {
            border-bottom: 2px solid var(--sf-primary-light);
            padding-bottom: 5px;
        }
        .sf-main-tabs .nav-link {
            color: var(--sf-text-muted);
            transition: var(--sf-transition);
            background: transparent;
        }
        .sf-main-tabs .nav-link:hover {
            color: var(--sf-primary);
            background: var(--sf-primary-light);
        }
        .sf-main-tabs .nav-link.active {
            background-color: var(--sf-primary) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 15px rgba(255,140,66,0.35);
        }
        [data-bs-theme="dark"] .sf-main-tabs {
            border-bottom-color: #2b3035;
        }

        /* --- Dashboard Container --- */
        .sf-dashboard .sf-filter-bar {
            background: var(--bs-body-bg);
            border-radius: var(--sf-radius);
            padding: 12px 20px;
            box-shadow: var(--sf-shadow);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .sf-dashboard .sf-filter-bar .sf-filter-btn {
            border: 1.5px solid #e0e0e0;
            background: transparent;
            color: var(--bs-body-color);
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--sf-transition);
        }
        .sf-dashboard .sf-filter-bar .sf-filter-btn:hover,
        .sf-dashboard .sf-filter-bar .sf-filter-btn.active {
            background: var(--sf-primary);
            color: #fff;
            border-color: var(--sf-primary);
            box-shadow: 0 2px 8px rgba(255,140,66,0.3);
        }
        [data-bs-theme="dark"] .sf-dashboard .sf-filter-bar .sf-filter-btn {
            border-color: #373b3e;
        }

        /* --- KPI Cards --- */
        .sf-dashboard .sf-kpi-card {
            background: var(--bs-body-bg);
            border-radius: var(--sf-radius);
            padding: 20px;
            box-shadow: var(--sf-shadow);
            transition: var(--sf-transition);
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
            animation: sfFadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        .sf-dashboard .sf-kpi-card:hover {
            box-shadow: var(--sf-shadow-hover);
            transform: translateY(-3px);
            border-color: var(--sf-primary);
        }
        .sf-dashboard .sf-kpi-card .sf-kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .sf-dashboard .sf-kpi-card .sf-kpi-value {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--bs-body-color);
        }
        .sf-dashboard .sf-kpi-card .sf-kpi-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--sf-text-muted);
        }
        .sf-dashboard .sf-kpi-card .sf-kpi-change {
            font-size: 0.78rem;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 3px;
        }
        .sf-dashboard .sf-kpi-change.up {
            background: rgba(46,204,113,0.12);
            color: var(--sf-success);
        }
        .sf-dashboard .sf-kpi-change.down {
            background: rgba(231,76,60,0.12);
            color: var(--sf-danger);
        }
        .sf-dashboard .sf-kpi-change.neutral {
            background: rgba(149,165,166,0.12);
            color: var(--sf-text-muted);
        }
        .sf-dashboard .sf-sparkline {
            margin-top: 10px;
            height: 30px;
        }
        .sf-dashboard .sf-sparkline svg {
            width: 100%;
            height: 30px;
        }

        /* --- Chart Cards --- */
        .sf-dashboard .sf-card {
            background: var(--bs-body-bg);
            border-radius: var(--sf-radius);
            padding: 24px;
            box-shadow: var(--sf-shadow);
            transition: var(--sf-transition);
            border: 1px solid transparent;
            height: 100%;
        }
        [data-bs-theme="dark"] .sf-dashboard .sf-card,
        [data-bs-theme="dark"] .sf-dashboard .sf-kpi-card {
            border-color: #2b3035;
        }
        .sf-dashboard .sf-card:hover {
            box-shadow: var(--sf-shadow-hover);
        }
        .sf-dashboard .sf-card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--bs-body-color);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sf-dashboard .sf-card-title i {
            color: var(--sf-primary);
        }

        /* --- Chart Tabs --- */
        .sf-dashboard .sf-chart-tabs {
            display: flex;
            gap: 4px;
            background: var(--sf-primary-light);
            border-radius: 10px;
            padding: 3px;
        }
        .sf-dashboard .sf-chart-tab {
            padding: 5px 14px;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--sf-text-muted);
            cursor: pointer;
            transition: var(--sf-transition);
        }
        .sf-dashboard .sf-chart-tab.active {
            background: var(--sf-primary);
            color: #fff;
            box-shadow: 0 2px 8px rgba(255,140,66,0.3);
        }

        /* --- Data Tables --- */
        .sf-dashboard .sf-table {
            font-size: 0.88rem;
        }
        .sf-dashboard .sf-table thead th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--sf-text-muted);
            font-weight: 700;
            border-bottom: 2px solid var(--sf-primary-light);
            padding: 10px 12px;
        }
        .sf-dashboard .sf-table tbody td {
            padding: 10px 12px;
            vertical-align: middle;
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }
        [data-bs-theme="dark"] .sf-dashboard .sf-table tbody td {
            border-bottom-color: rgba(255,255,255,0.04);
        }
        .sf-dashboard .sf-table tbody tr:hover {
            background: var(--sf-primary-light);
        }
        .sf-dashboard .sf-product-thumb {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid var(--sf-primary-light);
        }

        /* --- Status Badges --- */
        .sf-dashboard .sf-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .sf-badge-success { background: rgba(46,204,113,0.12); color: var(--sf-success); }
        .sf-badge-warning { background: rgba(241,196,15,0.12); color: #d4a800; }
        .sf-badge-danger { background: rgba(231,76,60,0.12); color: var(--sf-danger); }
        .sf-badge-info { background: rgba(52,152,219,0.12); color: var(--sf-info); }
        .sf-badge-primary { background: rgba(255,140,66,0.12); color: var(--sf-primary); }
        .sf-badge-purple { background: rgba(155,89,182,0.12); color: var(--sf-purple); }

        /* --- Inventory Alert Cards --- */
        .sf-dashboard .sf-inv-card {
            padding: 14px 16px;
            border-radius: var(--sf-radius-sm);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--sf-transition);
        }
        .sf-inv-green { background: rgba(46,204,113,0.08); border-left: 4px solid var(--sf-success); }
        .sf-inv-yellow { background: rgba(241,196,15,0.08); border-left: 4px solid var(--sf-warning); }
        .sf-inv-red { background: rgba(231,76,60,0.08); border-left: 4px solid var(--sf-danger); }

        /* --- Customer Avatar --- */
        .sf-dashboard .sf-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--sf-primary-light);
        }

        /* --- Performance Progress --- */
        .sf-dashboard .sf-progress-wrap {
            margin-bottom: 16px;
        }
        .sf-dashboard .sf-progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .sf-dashboard .sf-progress {
            height: 8px;
            border-radius: 10px;
            background: rgba(0,0,0,0.05);
            overflow: hidden;
        }
        [data-bs-theme="dark"] .sf-dashboard .sf-progress {
            background: rgba(255,255,255,0.08);
        }
        .sf-dashboard .sf-progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* --- Timeline --- */
        .sf-dashboard .sf-timeline {
            position: relative;
            padding-left: 28px;
        }
        .sf-dashboard .sf-timeline::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 4px;
            bottom: 4px;
            width: 2px;
            background: linear-gradient(to bottom, var(--sf-primary), var(--sf-pink));
            border-radius: 2px;
        }
        .sf-dashboard .sf-timeline-item {
            position: relative;
            padding: 8px 0 16px;
        }
        .sf-dashboard .sf-timeline-dot {
            position: absolute;
            left: -22px;
            top: 12px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid var(--bs-body-bg);
            box-shadow: 0 0 0 3px;
        }
        .sf-dashboard .sf-timeline-title {
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--bs-body-color);
        }
        .sf-dashboard .sf-timeline-desc {
            font-size: 0.82rem;
            color: var(--sf-text-muted);
        }
        .sf-dashboard .sf-timeline-time {
            font-size: 0.75rem;
            color: var(--sf-text-muted);
        }

        /* --- Notification Items --- */
        .sf-dashboard .sf-notif-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--sf-radius-sm);
            transition: var(--sf-transition);
            cursor: pointer;
        }
        .sf-dashboard .sf-notif-item:hover {
            background: var(--sf-primary-light);
        }
        .sf-dashboard .sf-notif-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sf-dashboard .sf-notif-badge {
            background: var(--sf-danger);
            color: #fff;
            min-width: 22px;
            height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 800;
            flex-shrink: 0;
        }

        /* --- Section Header --- */
        .sf-dashboard .sf-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .sf-dashboard .sf-section-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--bs-body-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sf-dashboard .sf-section-title i {
            color: var(--sf-primary);
            font-size: 1.1rem;
        }

        /* --- AI Card (enhanced) --- */
        .sf-dashboard .sf-ai-card {
            background: linear-gradient(135deg, var(--sf-primary-light) 0%, rgba(253,164,175,0.15) 50%, rgba(155,89,182,0.08) 100%);
            border-radius: var(--sf-radius);
            padding: 24px;
            box-shadow: var(--sf-shadow);
            border: 1px solid rgba(255,140,66,0.15);
            position: relative;
            overflow: hidden;
        }
        [data-bs-theme="dark"] .sf-dashboard .sf-ai-card {
            background: linear-gradient(135deg, rgba(255,140,66,0.08) 0%, rgba(155,89,182,0.06) 100%);
            border-color: #373b3e;
        }
        .sf-dashboard .sf-ai-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--sf-primary), var(--sf-pink), var(--sf-purple));
        }

        /* --- Mini Stat Cards (in inventory) --- */
        .sf-dashboard .sf-mini-stat {
            text-align: center;
            padding: 16px 8px;
            border-radius: var(--sf-radius-sm);
            background: var(--bs-body-bg);
            border: 1px solid rgba(0,0,0,0.05);
        }
        [data-bs-theme="dark"] .sf-dashboard .sf-mini-stat {
            border-color: #2b3035;
        }
        .sf-dashboard .sf-mini-stat .sf-mini-val {
            font-size: 1.4rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .sf-dashboard .sf-mini-stat .sf-mini-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--sf-text-muted);
            margin-top: 4px;
        }

        /* --- Animations --- */
        @keyframes sfFadeInUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .sf-dashboard .sf-kpi-card:nth-child(1) { animation-delay: 0.05s; }
        .sf-dashboard .sf-kpi-card:nth-child(2) { animation-delay: 0.1s; }
        .sf-dashboard .sf-kpi-card:nth-child(3) { animation-delay: 0.15s; }
        .sf-dashboard .sf-kpi-card:nth-child(4) { animation-delay: 0.2s; }
        .sf-dashboard .sf-kpi-card:nth-child(5) { animation-delay: 0.25s; }
        .sf-dashboard .sf-kpi-card:nth-child(6) { animation-delay: 0.3s; }
        .sf-dashboard .sf-kpi-card:nth-child(7) { animation-delay: 0.35s; }
        .sf-dashboard .sf-kpi-card:nth-child(8) { animation-delay: 0.4s; }

        .sf-anim-card {
            animation: sfFadeInUp 0.5s ease forwards;
            opacity: 0;
        }
        .sf-anim-card:nth-child(1) { animation-delay: 0.1s; }
        .sf-anim-card:nth-child(2) { animation-delay: 0.2s; }

        /* --- Scrollable container --- */
        .sf-scrollable {
            max-height: 400px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--sf-primary) transparent;
        }
        .sf-scrollable::-webkit-scrollbar { width: 5px; }
        .sf-scrollable::-webkit-scrollbar-track { background: transparent; }
        .sf-scrollable::-webkit-scrollbar-thumb { background: var(--sf-primary); border-radius: 10px; }

        /* --- Responsive Dashboard --- */
        @media (max-width: 1199px) {
            .sf-dashboard .sf-kpi-card .sf-kpi-value {
                font-size: 1.3rem;
            }
        }
        @media (max-width: 767px) {
            .sf-dashboard .sf-filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .sf-dashboard .sf-filter-bar .d-flex {
                flex-wrap: wrap;
                justify-content: center;
            }
            .sf-dashboard .sf-card {
                padding: 16px;
            }
            .sf-dashboard .sf-kpi-card {
                padding: 14px;
            }
            .sf-dashboard .sf-section-title {
                font-size: 1.05rem;
            }
        }
    </style>

    <script>
        const savedTheme = localStorage.getItem('adminTheme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    </script>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-2 sidebar d-none d-md-block">
            <div class="text-center py-4">
                <h4 class="fw-bold" style="color: var(--sunflower-orange);">Sun<span class="text-white">Flower</span></h4>
                <small class="text-muted">Admin Panel</small>
            </div>
            
            @php
                // --- KIỂM TRA QUYỀN TRUY CẬP ---
                $user = Auth::guard('nhanvien')->user();
                $isManager = $user->hasRole('Quản lý Cửa hàng');
                
                $canAccessProduct = $isManager || $user->hasRole('Quản lý Sản phẩm') || $user->hasRole('Quản lý Sản phẩm & Danh mục');
                $canAccessOrder = $isManager || $user->hasRole('Nhân viên Bán hàng');
                $canAccessKho = $isManager || $user->hasRole('Quản lý Kho hàng');
                $canAccessNhanVien = $isManager;
                $canAccessKhachHang = $isManager;
                $canAccessDanhGia = $isManager;
                $canAccessVoucher = $isManager;
            @endphp

            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i> Tổng quan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.lichlamviec.mySchedule') }}" class="nav-link {{ request()->is('admin/lich-cua-toi') ? 'active' : '' }}">
                        <i class="fa-regular fa-calendar-check"></i> Lịch của tôi
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ $canAccessProduct ? route('admin.products.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/products*') ? 'active' : '' }} {{ $canAccessProduct ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessProduct ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessProduct ? '' : 'Bạn không có quyền quản lý Sản phẩm' }}">
                        <div><i class="fa-solid fa-box"></i> Sản phẩm</div>
                        @if(!$canAccessProduct) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ $canAccessProduct ? route('admin.categories.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/categories*') ? 'active' : '' }} {{ $canAccessProduct ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessProduct ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessProduct ? '' : 'Bạn không có quyền quản lý Danh mục' }}">
                        <div><i class="fa-solid fa-list"></i> Danh mục</div>
                        @if(!$canAccessProduct) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ $canAccessProduct ? route('admin.nguyenlieu.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/nguyenlieu*') ? 'active' : '' }} {{ $canAccessProduct ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessProduct ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessProduct ? '' : 'Bạn không có quyền quản lý Nguyên liệu' }}">
                        <div><i class="fa-solid fa-leaf"></i> Nguyên liệu (BOM)</div>
                        @if(!$canAccessProduct) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="{{ $canAccessOrder ? route('admin.orders.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/orders*') ? 'active' : '' }} {{ $canAccessOrder ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessOrder ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessOrder ? '' : 'Bạn không có quyền quản lý Đơn hàng' }}">
                        <div><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</div>
                        @if(!$canAccessOrder) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                
                <li class="nav-item">
                    @php
                        $isKhoActive = request()->is('admin/inventory*') || request()->is('admin/phieunhapkho*') || request()->is('admin/longuyenlieu*') || request()->is('admin/phieuhuyhang*');
                    @endphp
                    <a href="{{ $canAccessKho ? '#collapseKhoHang' : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ $canAccessKho ? '' : 'disabled text-muted' }}" 
                       data-bs-toggle="{{ $canAccessKho ? 'collapse' : '' }}" 
                       role="button" 
                       aria-expanded="{{ $isKhoActive ? 'true' : 'false' }}"
                       style="{{ $canAccessKho ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}">
                        <div><i class="fa-solid fa-warehouse me-1"></i> Kho hàng</div>
                        @if($canAccessKho)
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.8em;"></i>
                        @else
                            <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i>
                        @endif
                    </a>
                    @if($canAccessKho)
                    <div class="collapse {{ $isKhoActive ? 'show' : '' }}" id="collapseKhoHang">
                        <ul class="nav flex-column ms-3 mt-1" style="font-size: 0.95em;">
                            <li class="nav-item">
                                <a href="{{ route('admin.inventory.logs') }}" class="nav-link {{ request()->is('admin/inventory/logs') ? 'active' : '' }}" style="padding: 8px 15px;">
                                    <i class="fa-solid fa-clock-rotate-left me-2"></i> Lịch sử Kho BOM
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.longuyenlieu.index') }}" class="nav-link {{ request()->is('admin/longuyenlieu*') ? 'active' : '' }}" style="padding: 8px 15px;">
                                    <i class="fa-solid fa-cubes-stacked me-2"></i> Quản lý Lô Nguyên liệu
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.phieunhapkho.index') }}" class="nav-link {{ request()->is('admin/phieunhapkho*') ? 'active' : '' }}" style="padding: 8px 15px;">
                                    <i class="fa-solid fa-box-open me-2"></i> Nhập kho BOM
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.phieuhuyhang.index') }}" class="nav-link {{ request()->is('admin/phieuhuyhang*') ? 'active' : '' }}" style="padding: 8px 15px;">
                                    <i class="fa-solid fa-file-circle-xmark me-2"></i> Phiếu Hủy Hàng
                                </a>
                            </li>

                        </ul>
                    </div>
                    @endif
                </li>
                
                <li class="nav-item">
                    @php
                        $isNhanVienActive = request()->is('admin/nhanvien*') || request()->is('admin/vaitro*') || request()->is('admin/lichlamviec*');
                    @endphp
                    <a href="{{ $canAccessNhanVien ? '#collapseNhanVien' : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ $canAccessNhanVien ? '' : 'disabled text-muted' }}" 
                       data-bs-toggle="{{ $canAccessNhanVien ? 'collapse' : '' }}" 
                       role="button" 
                       aria-expanded="{{ $isNhanVienActive ? 'true' : 'false' }}"
                       style="{{ $canAccessNhanVien ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}">
                        <div><i class="fa-solid fa-users me-1"></i> Nhân viên</div>
                        @if($canAccessNhanVien)
                            <i class="fa-solid fa-chevron-down" style="font-size: 0.8em;"></i>
                        @else
                            <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i>
                        @endif
                    </a>
                    @if($canAccessNhanVien)
                    <div class="collapse {{ $isNhanVienActive ? 'show' : '' }}" id="collapseNhanVien">
                        <ul class="nav flex-column ms-3 mt-1" style="font-size: 0.95em;">
                            <li class="nav-item">
                                <a href="{{ route('admin.nhanvien.index') }}" class="nav-link {{ request()->is('admin/nhanvien*') ? 'active' : '' }}" style="padding: 8px 15px;">
                                    <i class="fa-solid fa-user-shield me-2"></i> Danh sách NV
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.lichlamviec.index') }}" class="nav-link {{ request()->is('admin/lichlamviec*') ? 'active' : '' }}" style="padding: 8px 15px;">
                                    <i class="fa-solid fa-users-cog me-2"></i> Quản lý Phân ca
                                </a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </li>
                <li class="nav-item">
                    <a href="{{ $canAccessKhachHang ? route('admin.khachhang.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/khachhang*') ? 'active' : '' }} {{ $canAccessKhachHang ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessKhachHang ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessKhachHang ? '' : 'Chỉ Quản lý Cửa hàng mới có quyền xem' }}">
                        <div><i class="fa-solid fa-users-viewfinder"></i> Khách hàng</div>
                        @if(!$canAccessKhachHang) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ $canAccessDanhGia ? route('admin.danhgia.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/danhgia*') ? 'active' : '' }} {{ $canAccessDanhGia ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessDanhGia ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessDanhGia ? '' : 'Chỉ Quản lý Cửa hàng mới có quyền xem' }}">
                        <div><i class="fa-solid fa-star"></i> Đánh giá & Phản hồi</div>
                        @if(!$canAccessDanhGia) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ $canAccessVoucher ? route('admin.vouchers.index') : '#' }}" 
                       class="nav-link d-flex justify-content-between align-items-center {{ request()->is('admin/vouchers*') ? 'active' : '' }} {{ $canAccessVoucher ? '' : 'disabled text-muted' }}"
                       style="{{ $canAccessVoucher ? '' : 'pointer-events: none; opacity: 0.5; cursor: not-allowed;' }}"
                       title="{{ $canAccessVoucher ? '' : 'Chỉ Quản lý Cửa hàng mới có quyền quản lý Mã giảm giá' }}">
                        <div><i class="fa-solid fa-ticket"></i> Mã giảm giá</div>
                        @if(!$canAccessVoucher) <i class="fa-solid fa-lock text-secondary" style="font-size: 0.8em;"></i> @endif
                    </a>
                </li>
                <li class="nav-item mt-5">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <div class="col-md-10">
            <div class="topbar d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold">@yield('page_title')</h5>
                
                <div class="user-info d-flex align-items-center gap-3">
                    
                    {{-- Global Notifications --}}
                    @php
                        $today = \Carbon\Carbon::today();
                        
                        // Chỉ hiển thị các thông báo CẦN XỬ LÝ (Actionable)
                        $pendingOrdersCount = \App\Models\DonHang::where('trangthai', 'Chờ xác nhận')->count();
                        
                        $expiringBatchesCount = \App\Models\LoNguyenLieu::where('trangthai', '!=', 'Hết hàng')
                            ->whereNotNull('hsd')
                            ->where('hsd', '<=', \Carbon\Carbon::now()->addDays(7))
                            ->where('hsd', '>', \Carbon\Carbon::now())
                            ->count();
                        
                        $totalNotifs = $pendingOrdersCount + $expiringBatchesCount;
                    @endphp
                    
                    <div class="dropdown">
                        <button class="btn btn-link text-dark position-relative p-0 border-0" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--bs-body-color) !important;">
                            <i class="fa-regular fa-bell" style="font-size: 1.3rem;"></i>
                            @if($totalNotifs > 0)
                                <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle bg-danger border border-light rounded-circle" style="width: 10px; height: 10px; transition: opacity 0.2s; margin-top: 6px; margin-left: -4px;"></span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" style="width: 320px; max-height: 400px; overflow-y: auto; border-radius: 12px; margin-top: 10px;">
                            <li><h6 class="dropdown-header fw-bold border-bottom pb-2 mb-2" style="font-size: 0.95rem; color: var(--sf-primary);">Thông báo ({{ $totalNotifs }})</h6></li>
                            @if($pendingOrdersCount > 0)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('admin.orders.index') }}">
                                        <div class="me-3 flex-shrink-0" style="width: 38px; height: 38px; border-radius: 10px; background: rgba(52, 152, 219, 0.1); display: flex; align-items: center; justify-content: center; color: #3498DB;">
                                            <i class="fa-solid fa-shopping-bag"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.9rem; color: var(--bs-body-color);">{{ $pendingOrdersCount }} Đơn hàng cần xác nhận</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">Đang chờ xử lý</small>
                                        </div>
                                    </a>
                                </li>
                            @endif
                            @if($expiringBatchesCount > 0)
                                <li>
                                    <a class="dropdown-item d-flex align-items-center py-2 px-3" href="{{ route('admin.longuyenlieu.index') }}">
                                        <div class="me-3 flex-shrink-0" style="width: 38px; height: 38px; border-radius: 10px; background: rgba(241, 196, 15, 0.1); display: flex; align-items: center; justify-content: center; color: #F1C40F;">
                                            <i class="fa-solid fa-clock"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.9rem; color: var(--bs-body-color);">{{ $expiringBatchesCount }} Lô sắp hết hạn</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">Cần xử lý gấp</small>
                                        </div>
                                    </a>
                                </li>
                            @endif
                            @if($totalNotifs == 0)
                                <li><div class="dropdown-item text-center text-muted py-4"><i class="fa-regular fa-bell-slash fs-4 mb-2 d-block"></i> Không có thông báo mới</div></li>
                            @endif
                        </ul>
                    </div>

                    <button id="themeToggleBtn" class="theme-toggle-btn" title="Chuyển đổi giao diện Sáng/Tối">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    
                    <div class="d-flex align-items-center border-start ps-3">
                        <span class="me-3 fw-semibold">Chào, {{ Auth::guard('nhanvien')->user()->hoten }}</span>
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('nhanvien')->user()->hoten) }}&background=FF8C00&color=fff" class="rounded-circle shadow-sm" width="40" alt="avatar">
                    </div>
                </div>
            </div>

            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        const themeIcon = themeToggleBtn.querySelector('i');
        
        // Hàm cập nhật icon dựa theo theme hiện tại
        function updateIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
                themeIcon.style.color = '#FFD700'; // Màu vàng cho mặt trời
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
                themeIcon.style.color = ''; // Reset màu
            }
        }

        // Cập nhật icon lúc mới load trang
        updateIcon(document.documentElement.getAttribute('data-bs-theme'));

        // Sự kiện khi bấm nút
        themeToggleBtn.addEventListener('click', function() {
            let currentTheme = document.documentElement.getAttribute('data-bs-theme');
            let newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            // Áp dụng theme mới
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('adminTheme', newTheme);
            updateIcon(newTheme);
        });

        // Notification Badge Logic
        const notifDropdownBtn = document.getElementById('notificationDropdown');
        const notifBadge = document.getElementById('notifBadge');
        
        if (notifDropdownBtn && notifBadge) {
            const currentTotal = parseInt('{{ $totalNotifs ?? 0 }}');
            let lastTotal = parseInt(localStorage.getItem('lastTotalNotifs') || '0');
            let hasSeen = localStorage.getItem('hasSeenCurrentNotifs') === 'true';
            
            // Nếu có thông báo mới (số lượng hiện tại > số lượng đã lưu trước đó)
            if (currentTotal > lastTotal) {
                hasSeen = false;
                localStorage.setItem('hasSeenCurrentNotifs', 'false');
            }
            
            // Cập nhật lại số lượng tổng mới nhất
            localStorage.setItem('lastTotalNotifs', currentTotal.toString());
            
            // Nếu không có thông báo, hoặc đã xem rồi -> Ẩn badge
            if (currentTotal === 0 || hasSeen) {
                notifBadge.style.display = 'none';
            } else {
                notifBadge.style.display = 'inline-block';
            }

            // Khi người dùng click vào cái chuông
            notifDropdownBtn.addEventListener('click', function() {
                if (!hasSeen && currentTotal > 0) {
                    localStorage.setItem('hasSeenCurrentNotifs', 'true');
                    hasSeen = true;
                    notifBadge.style.opacity = '0';
                    setTimeout(() => notifBadge.style.display = 'none', 200);
                }
            });
        }
    });
</script>

@stack('scripts')

</body>
</html>