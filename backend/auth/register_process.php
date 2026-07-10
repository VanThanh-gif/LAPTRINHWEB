<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🎯 CHỖ SỬA 1: Từ vị trí backend/auth/ nhảy ngược ra 2 cấp để lấy file kết nối database chung của cả nhóm
require_once __DIR__ . '/../../config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ thông tin đăng ký!'); window.history.back();</script>";
        exit();
    }

    try {
        // Kiểm tra xem email này đã có ai đăng ký trước đó chưa
        $checkEmail = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $checkEmail->execute([$email]);
        
        if ($checkEmail->fetch()) {
            echo "<script>alert('Email này đã tồn tại trong hệ thống! Vui lòng dùng email khác.'); window.history.back();</script>";
            exit();
        }

        // Tiến hành chèn tài khoản mới vào database (Mặc định vai trò là 'user' và trạng thái 'active')
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'user', 'active')");
        $stmt->execute([$username, $email, $password]);

        // 🎯 CHỖ SỬA 2: Đăng ký thành công thì điều hướng người dùng quay trở lại trang đăng nhập mới tinh
        echo "<script>alert('Đăng ký tài khoản thành công!'); window.location.href = '/LAPTRINHWEB/frontend/guest/login.php';</script>";
        exit();

    } catch (PDOException $e) {
        die("Lỗi hệ thống khi đăng ký: " . $e->getMessage());
    }
} else {
    // 🎯 CHỖ SỬA 3: Nếu ai đó cố tình truy cập trực tiếp file này, đẩy họ về trang đăng ký giao diện
    header("Location: /LAPTRINHWEB/frontend/guest/register.php");
    exit();
}