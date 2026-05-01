<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')  SunFlower Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Nunito', sans-serif; background-color: #f8f9fa; }
        :root { --sunflower-orange: #FF8C00; --admin-sidebar: #2c3e50; }
        
        .sidebar {
            min-height: 100vh;
            background: var(--admin-sidebar);
            color: white;
            transition: all 0.3s;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-radius: 5px;
            margin: 5px 15px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sunflower-orange);
            color: white;
        }
        .sidebar .nav-link i { width: 25px; }
        
        .topbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
        }
        .content-wrapper { padding: 30px; }
        .btn-sun { background: var(--sunflower-orange); color: white; }
        .btn-sun:hover { background: #e67e00; color: white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container-fluid p-0">
    <div class="row g-0">
        <div class="col-md-2 sidebar d-none d-md-block">
            <div class="text-center py-4">
                <h4 class="fw-bold" style="color: var(--sunflower-orange);">Sun<span class="text-white">Flower</span></h4>
                <small class="text-muted">Admin Panel</small>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i> Tổng quan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.products.index') }}" class="nav-link"><i class="fa-solid fa-box"></i> Sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link"><i class="fa-solid fa-list"></i> Danh mục</a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.orders.index') }}" class="nav-link"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
                </li>
                <li class="nav-item">
    @php
        // Kiểm tra xem URL hiện tại có thuộc phần Kho hàng không để tự động mở menu
        $isKhoActive = request()->is('admin/lohang*') || request()->is('admin/phieuhuyhang*');
    @endphp
    
    <!-- Nút Kho hàng chính (bấm vào để xổ xuống) -->
    <a href="#collapseKhoHang" class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isKhoActive ? 'true' : 'false' }}">
        <div><i class="fa-solid fa-warehouse me-1"></i> Kho hàng</div>
        <i class="fa-solid fa-chevron-down" style="font-size: 0.8em;"></i>
    </a>
    
    <!-- Phần Menu con xổ xuống -->
    <div class="collapse {{ $isKhoActive ? 'show' : '' }}" id="collapseKhoHang">
        <ul class="nav flex-column ms-3 mt-1" style="font-size: 0.95em;">
            <li class="nav-item">
                <a href="{{ route('admin.lohang.index') }}" class="nav-link {{ request()->is('admin/lohang*') ? 'active' : '' }}" style="padding: 8px 15px;">
                    <i class="fa-solid fa-boxes-packing me-2"></i> Phiếu nhập kho
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.phieuhuyhang.index') }}" class="nav-link {{ request()->is('admin/phieuhuyhang*') ? 'active' : '' }}" style="padding: 8px 15px;">
                    <i class="fa-solid fa-file-circle-xmark me-2"></i> Phiếu hủy hàng
                </a>
            </li>
        </ul>
    </div>
</li>
                <li class="nav-item">
                    <a href="#" class="nav-link"><i class="fa-solid fa-users"></i> Nhân viên</a>
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
                <div class="user-info d-flex align-items-center">
                    <span class="me-3 fw-semibold">Chào, {{ Auth::guard('nhanvien')->user()->hoten }}</span>
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('nhanvien')->user()->hoten) }}&background=FF8C00&color=fff" class="rounded-circle" width="40" alt="avatar">
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
</body>
</html>