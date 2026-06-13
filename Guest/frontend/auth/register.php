<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng Ký Thành Viên - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #e0f2fe, #f0fdf4); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        .register-card { background: white; border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 440px; padding: 35px 30px; }
        .btn-custom { background-color: #2563eb; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; width: 100%; }
        .btn-custom:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>

<div class="register-card">
    <div class="text-center mb-4">
        <h3 class="fw-bold text-primary mb-1">TẠO TÀI KHOẢN</h3>
        <small class="text-muted">Đăng ký thành viên Sinh viên tham gia hệ thống</small>
    </div>

    <form action="/AIStudyHub/Guest/backend/auth/register_process.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold">Họ và tên</label>
            <input type="text" name="username" class="form-control" placeholder="Nhập họ tên của bạn..." required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Địa chỉ Email</label>
            <input type="email" name="email" class="form-control" placeholder="sinhvien@example.com" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold">Mật khẩu</label>
            <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự..." required>
        </div>
        <button type="submit" class="btn btn-custom mb-3">Kích Hoạt Đăng Ký</button>
    </form>

    <div class="text-center">
        <span class="text-muted">Đã có tài khoản?</span> 
        <a href="/AIStudyHub/Guest/frontend/auth/login.php" class="text-primary fw-bold text-decoration-none ms-1">Đăng nhập</a>
    </div>
</div>

</body>
</html>