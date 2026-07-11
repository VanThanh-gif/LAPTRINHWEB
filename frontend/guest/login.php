<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Nếu đã đăng nhập, đẩy về đúng trang
if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/document.php");
    }
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

$message = ''; $msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = 'Vui lòng nhập đầy đủ thông tin!';
        $msg_type = 'warning';
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
                // CẤP PHÁT SESSION ĐẦY ĐỦ
                $_SESSION['user_id'] = $user['user_id'] ?? $user['id'] ?? $user['ma_tk'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'] ?? $user['quyen'] ?? 'user';
                $_SESSION['email'] = $user['email'];
                
                // Lấy Avatar từ DB lưu vào Session để Navbar hiển thị
                $_SESSION['avatar'] = $user['avatar'] ?? null; 
                
                header("Location: " . ($_SESSION['role'] === 'admin' ? "../admin/dashboard.php" : "../user/document.php"));
                exit();
            } else {
                $message = 'Email hoặc mật khẩu không chính xác!';
                $msg_type = 'danger';
            }
        } catch (PDOException $e) {
            $message = 'Lỗi hệ thống!'; $msg_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: url('https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat fixed;
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .overlay { position: absolute; inset: 0; background: rgba(15, 23, 42, 0.7); backdrop-filter: blur(10px); z-index: 1; }
        .auth-wrapper { position: relative; z-index: 2; width: 100%; max-width: 450px; padding: 20px; }
        .auth-card { background: rgba(255, 255, 255, 0.95); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(20px); }
        
        .brand-logo { width: 60px; height: 60px; background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 28px; color: white; margin: 0 auto 20px; box-shadow: 0 10px 20px -5px rgba(67, 24, 255, 0.4); }
        .form-floating > .form-control { border-radius: 14px; border: 1.5px solid #e2e8f0; padding-left: 20px; font-weight: 500; }
        .form-floating > .form-control:focus { border-color: #4318FF; box-shadow: 0 0 0 4px rgba(67, 24, 255, 0.1); }
        .form-floating > label { padding-left: 20px; color: #64748b; font-weight: 500; }
        
        .btn-submit { background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%); border: none; color: white; border-radius: 14px; padding: 16px; font-weight: 700; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 10px 20px -5px rgba(67, 24, 255, 0.3); }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(67, 24, 255, 0.5); color: white; }
        .link-custom { color: #4318FF; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .link-custom:hover { color: #1e0a80; }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="auth-wrapper">
        <div class="text-center mb-4">
            <a href="../user/document.php" class="text-white text-decoration-none fw-medium opacity-75 hover-opacity-100 transition">
                <i class="bi bi-arrow-left me-2"></i>Quay lại trang chủ
            </a>
        </div>
        <div class="auth-card">
            <div class="text-center mb-4">
                <div class="brand-logo"><i class="bi bi-layers-fill"></i></div>
                <h3 class="fw-bolder text-dark mb-1">Mừng bạn trở lại!</h3>
                <p class="text-muted fw-medium fs-6">Đăng nhập để tiếp tục học tập</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $msg_type ?> rounded-3 fw-bold small text-center border-0 shadow-sm"><i class="bi bi-exclamation-circle-fill me-2"></i><?= $message ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="emailInput" name="email" placeholder="name@example.com" required>
                    <label for="emailInput">Địa chỉ Email</label>
                </div>
                
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="passInput" name="password" placeholder="Mật khẩu" required>
                    <label for="passInput">Mật khẩu</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label text-muted small fw-medium" for="rememberMe">Ghi nhớ tôi</label>
                    </div>
                    <a href="#" class="link-custom small">Quên mật khẩu?</a>
                </div>

                <button type="submit" class="btn btn-submit w-100 mb-4">Đăng Nhập Khám Phá</button>

                <p class="text-center text-muted fw-medium small mb-0">
                    Chưa có tài khoản? <a href="register.php" class="link-custom ms-1">Tạo ngay miễn phí</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>