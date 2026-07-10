<?php
// Bật lỗi để dễ fix bug
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Kiểm tra rỗng
    if (empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ email và mật khẩu!'); window.history.back();</script>";
        exit();
    }

    // ============================================================
    // 1. TÀI KHOẢN ĐẶC QUYỀN (SUPER ADMIN) ĐƯỢC QUY ĐỊNH CỨNG
    // ============================================================
    if ($email === 'admin@aistudyhub.com' && $password === 'admin123') {
        
        // Cấp phát Session đặc quyền
        $_SESSION['user_id'] = 9999; // ID ảo dành riêng cho Super Admin
        $_SESSION['username'] = 'Quản trị viên Hệ thống';
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'admin'; // Cờ 'admin' để vượt qua các lớp bảo mật

        // Chuyển hướng thẳng vào Bảng điều khiển Quản trị
        echo "<script>
            window.location.href = '../../frontend/admin/dashboard.php';
        </script>";
        exit();
    }

    // ============================================================
    // 2. NẾU KHÔNG PHẢI ADMIN -> KIỂM TRA TÀI KHOẢN USER TRONG DB
    // ============================================================
    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Hỗ trợ kiểm tra cả mật khẩu đã mã hóa (password_hash) hoặc mật khẩu thô (text)
            $isPasswordCorrect = false;
            
            if (password_verify($password, $user['password'])) {
                $isPasswordCorrect = true;
            } elseif ($password === $user['password']) {
                $isPasswordCorrect = true;
            }

            if ($isPasswordCorrect) {
                // Đăng nhập User thành công
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'] ?? 'user';

                // User bình thường thì về Kho tài liệu
                echo "<script>window.location.href = '../../frontend/user/document.php';</script>";
                exit();
            } else {
                echo "<script>alert('Mật khẩu không chính xác!'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Tài khoản không tồn tại trong hệ thống!'); window.history.back();</script>";
            exit();
        }
    } catch (PDOException $e) {
        die("Lỗi cơ sở dữ liệu: " . $e->getMessage());
    }

} else {
    // Nếu gõ link trực tiếp thì đá về form đăng nhập
    header("Location: ../../frontend/guest/login.php");
    exit();
}
?>