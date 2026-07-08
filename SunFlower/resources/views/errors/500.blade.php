<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi hệ thống | SunFlower</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fdf6e3 0%, #fff8f0 50%, #fef3e2 100%);
            color: #333;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .error-icon {
            font-size: 5rem;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #e74c3c;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        .error-message {
            font-size: 1rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-home {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #f39c12, #e67e22);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(230, 126, 34, 0.3);
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(230, 126, 34, 0.4);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">🔧</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Hệ thống đang gặp sự cố</h1>
        <p class="error-message">
            Xin lỗi, hệ thống đang gặp lỗi kỹ thuật. Đội ngũ kỹ thuật của SunFlower
            đã được thông báo và đang khắc phục. Vui lòng thử lại sau ít phút.
        </p>
        <a href="{{ url('/') }}" class="btn-home">🏠 Về trang chủ</a>
    </div>
</body>
</html>
