<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// SỬA LỖI DÒNG 1: Kết nối database an toàn tuyệt đối không lo sai cấp thư mục
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #e0f2fe, #f0fdf4); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .login-card { background: white; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 420px; padding: 40px 30px; }
        .btn-custom { background-color: #16a34a; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; transition: 0.2s; }
        .btn-custom:hover { background-color: #15803d; color: white; }
    </style>
</head>
<body>

<div class="login-card">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-success mb-1">AI STUDY HUB</h3>
        <small class="text-muted">Hệ thống chia sẻ tài liệu trực tuyến</small>
    </div>

    <form action="/AIStudyHub/Guest/backend/auth/login_process.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Email tài khoản</label>
            <input type="email" name="email" class="form-control py-2" placeholder="name@example.com" required>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Mật khẩu</label>
            <input type="password" name="password" class="form-control py-2" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-custom mb-3">Đăng Nhập System</button>
    </form>

    <div class="text-center mt-3">
        <span class="text-muted">Chưa có tài khoản?</span> 
        <a href="/AIStudyHub/Guest/frontend/auth/register.php" class="text-success fw-bold text-decoration-none ms-1">Đăng ký ngay</a>
    </div>
</div>

</body>
</html>