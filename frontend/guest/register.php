<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../user/document.php"); exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

$message = ''; $msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $message = 'Vui lòng điền đầy đủ thông tin!';
        $msg_type = 'warning';
    } else {
        try {
            // Kiểm tra xem email đã tồn tại chưa
            $check_stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
            $check_stmt->execute([$email]);
            if ($check_stmt->fetch()) {
                $message = 'Email này đã được đăng ký. Vui lòng dùng email khác!';
                $msg_type = 'danger';
            } else {
                // Mã hóa mật khẩu cho an toàn (Chuẩn bảo mật đồ án)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Mặc định người dùng mới tạo luôn là 'user' (sinh viên)
                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                $insert_stmt->execute([$username, $email, $hashed_password]);
                
                $message = 'Đăng ký thành công! Đang chuyển hướng...';
                $msg_type = 'success';
                
                // Chuyển tới login sau 2 giây
                echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
            }
        } catch (PDOException $e) {
            $message = 'Lỗi hệ thống: Không thể đăng ký lúc này.'; $msg_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - AI Study Hub</title>
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
        .auth-wrapper { position: relative; z-index: 2; width: 100%; max-width: 500px; padding: 20px; }
        .auth-card { background: rgba(255, 255, 255, 0.95); border-radius: 24px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(20px); }
        
        .brand-logo { width: 50px; height: 50px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; margin: 0 auto 15px; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4); }
        .form-floating > .form-control { border-radius: 14px; border: 1.5px solid #e2e8f0; padding-left: 20px; font-weight: 500; }
        .form-floating > .form-control:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        .form-floating > label { padding-left: 20px; color: #64748b; font-weight: 500; }
        
        .btn-submit { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: white; border-radius: 14px; padding: 16px; font-weight: 700; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.3); }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 25px -5px rgba(16, 185, 129, 0.5); color: white; }
        .link-custom { color: #10b981; font-weight: 600; text-decoration: none; transition: 0.2s; }
        .link-custom:hover { color: #047857; }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="text-center mb-4">
                <div class="brand-logo"><i class="bi bi-person-plus-fill"></i></div>
                <h3 class="fw-bolder text-dark mb-1">Gia nhập cộng đồng!</h3>
                <p class="text-muted fw-medium fs-6">Hàng ngàn tài liệu chất lượng đang chờ bạn</p>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?= $msg_type ?> rounded-3 fw-bold small text-center border-0 shadow-sm"><i class="bi bi-info-circle-fill me-2"></i><?= $message ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="nameInput" name="username" placeholder="Họ và tên" required>
                    <label for="nameInput">Họ và tên của bạn</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="emailInput" name="email" placeholder="name@example.com" required>
                    <label for="emailInput">Địa chỉ Email</label>
                </div>
                
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="passInput" name="password" placeholder="Mật khẩu" minlength="6" required>
                    <label for="passInput">Mật khẩu (Tối thiểu 6 ký tự)</label>
                </div>

                <button type="submit" class="btn btn-submit w-100 mb-4">Tạo Tài Khoản Mới</button>

                <p class="text-center text-muted fw-medium small mb-0">
                    Đã có tài khoản? <a href="login.php" class="link-custom ms-1">Đăng nhập ngay</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>