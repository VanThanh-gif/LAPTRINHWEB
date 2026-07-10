<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Chặn nếu đã đăng nhập rồi thì không cho vào trang quên mật khẩu nữa
if (isset($_SESSION['user_id'])) {
    header("Location: ../user/document.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* GIỮ NGUYÊN HIỆU ỨNG NỀN CHUYỂN ĐỘNG CHÉO */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(-45deg, #f8fafc, #e2e8f0, #ffffff, #cbd5e1);
            background-size: 400% 400%;
            animation: gradientMove 12s ease infinite;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .forgot-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            padding: 50px 40px;
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        
        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        }

        .form-floating > .form-control {
            border-radius: 12px; border: 1.5px solid #e2e8f0; padding-left: 20px; background: transparent;
        }
        .form-floating > .form-control:focus {
            border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); background: #fff;
        }
        .form-floating > label { padding-left: 20px; color: #64748b; }
        
        .btn-submit {
            background-color: #2563eb; color: white; border-radius: 12px; padding: 16px;
            font-weight: 700; font-size: 1rem; transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #1d4ed8; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2); color: white;
        }
        .text-link { color: #64748b; font-weight: 600; text-decoration: none; transition: 0.2s;}
        .text-link:hover { color: #0f172a; text-decoration: underline; }
    </style>
</head>
<body>

<div class="forgot-card">
    <div class="icon-circle">
        <i class="bi bi-shield-lock-fill text-primary" style="font-size: 2.5rem;"></i>
    </div>
    
    <h3 class="fw-bold text-dark mb-2">Khôi Phục Mật Khẩu</h3>
    <p class="text-muted mb-4 px-3">Đừng lo lắng! Vui lòng nhập địa chỉ email bạn đã đăng ký, chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu cho bạn.</p>

    <form action="../../backend/auth/forgot_password_process.php" method="POST">
        
        <div class="form-floating mb-4 text-start">
            <input type="email" name="email" class="form-control" id="emailInput" placeholder="name@example.com" required>
            <label for="emailInput">Nhập địa chỉ Email của bạn</label>
        </div>

        <button type="submit" class="btn btn-submit w-100 mb-4">Gửi Yêu Cầu <i class="bi bi-send ms-2"></i></button>
    </form>

    <div class="text-center mt-2">
        <a href="login.php" class="text-link"><i class="bi bi-arrow-left me-1"></i> Quay lại trang Đăng nhập</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>