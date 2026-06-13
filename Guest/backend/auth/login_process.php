<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SỬA LỖI ĐƯỜNG DẪN: Từ vị trí Guest/backend/auth/ nhảy ra 3 cấp để vào thư mục config
require_once __DIR__ . '/../../../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin!'); window.history.back();</script>";
        exit();
    }

    try {
        // Kiểm tra tài khoản trong database
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Hỗ trợ cả kiểm tra mật khẩu thô và mật khẩu mã hóa để tránh lỗi đăng nhập
        if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
            
            // Lưu dữ liệu vào Session hệ thống
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['role']      = $user['role'];

            // Điều hướng phân quyền chuẩn theo yêu cầu của bạn
            if ($user['role'] === 'admin') {
                header("Location: /AIStudyHub/Admin/frontend/dashboard/dashboard.php");
            } else {
                header("Location: /AIStudyHub/User/frontend/documents/my_documents.php");
            }
            exit();
        } else {
            echo "<script>alert('Email hoặc Mật khẩu không chính xác! Vui lòng thử lại.'); window.history.back();</script>";
            exit();
        }
    } catch (PDOException $e) {
        die("Lỗi xử lý hệ thống: " . $e->getMessage());
    }
} else {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}