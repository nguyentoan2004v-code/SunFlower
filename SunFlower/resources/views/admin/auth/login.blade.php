<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị - SunFlower</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            /* Background gradient sang trọng (Tông Xanh rêu/Đen) */
            background: linear-gradient(135deg, #112A34, #1D4350, #2980B9);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Nunito', sans-serif;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 950px;
            display: flex;
            flex-direction: row;
        }
        /* Hình ảnh bên trái */
        .login-image {
            /* Thay link ảnh này bằng ảnh cánh đồng hoa hướng dương của bạn nếu có */
            background: url('https://images.unsplash.com/photo-1595859703065-42ec16223403?q=80&w=800&auto=format&fit=crop') center/cover;
            width: 50%;
            display: none; /* Ẩn trên mobile */
            position: relative;
        }
        .image-overlay {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background:rgba(29, 67, 80, 0.65); /* Phủ mờ một lớp màu xanh */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        @media (min-width: 768px) {
            .login-image { display: block; }
        }
        /* Khu vực Form đăng nhập */
        .login-form {
            width: 100%;
            padding: 3rem;
        }
        @media (min-width: 768px) {
            .login-form { width: 50%; padding: 4rem; }
        }
        .logo-text {
            font-weight: 800;
            color: #1D4350;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        .logo-text span {
            color: #F1C40F; /* Màu vàng SunFlower */
        }
        .form-floating > label {
            color: #6c757d;
        }
        .form-control {
            border-radius: 10px;
        }
        .form-control:focus {
            border-color: #2980B9;
            box-shadow: 0 0 0 0.25rem rgba(41, 128, 185, 0.2);
        }
        .btn-login {
            background: linear-gradient(to right, #1D4350, #2980B9);
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(41, 128, 185, 0.4);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="login-card">
                    <div class="login-image">
                        <div class="image-overlay">
                            <i class="fa-solid fa-seedling fa-4x mb-3" style="color: #F1C40F;"></i>
                            <h2 style="font-weight: 800; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">Quản Lý Thông Minh</h2>
                            <p style="font-size: 1.1rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Hệ thống quản trị nội bộ dành riêng cho đội ngũ nhân sự SunFlower.</p>
                        </div>
                    </div>
                    
                    <div class="login-form">
                        <div class="text-center mb-4">
                            <h3 class="logo-text">Sun<span>Flower</span> Admin</h3>
                            <p class="text-muted">Chào mừng trở lại! Vui lòng đăng nhập.</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3" style="font-size: 0.9rem;">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.login.post') }}" autocomplete="off">
                            @csrf
                            <div class="form-floating mb-3">
                                <input class="form-control" id="email" name="email" type="email" placeholder="name@example.com" value="{{ old('email') }}" required autofocus autocomplete="off" />
                                <label for="email"><i class="fa-regular fa-envelope me-2 text-muted"></i>Email nhân viên</label>
                            </div>
                            
                            <div class="form-floating mb-4">
                                <input class="form-control" id="password" name="password" type="password" placeholder="Mật khẩu" required autocomplete="new-password" />
                                <label for="password"><i class="fa-solid fa-lock me-2 text-muted"></i>Mật khẩu</label>
                            </div>
                            
                            <div class="d-flex align-items-center justify-content-between mb-4" style="font-size: 0.9rem;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="rememberPasswordCheck">
                                    <label class="form-check-label text-muted" for="rememberPasswordCheck">
                                        Ghi nhớ đăng nhập
                                    </label>
                                </div>
                                <a class="text-decoration-none fw-bold" href="#" style="color: #2980B9;">Quên mật khẩu?</a>
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                                ĐĂNG NHẬP <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>