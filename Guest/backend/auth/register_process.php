<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); 
    
    // Mặc định tài khoản đăng ký mới từ Form sẽ mang quyền user (Sinh viên)
    $role = 'user'; 
    $status = 'active';

    if (empty($username) || empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đầy đủ các trường dữ liệu!'); window.history.back();</script>";
        exit();
    }

    try {
        // Kiểm tra xem Email đăng ký này đã tồn tại trong CSDL chưa
        $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? LIMIT 1");
        $check->execute([$email]);
        if ($check->rowCount() > 0) {
            echo "<script>alert('Email này đã được sử dụng! Vui lòng chọn Email khác.'); window.history.back();</script>";
            exit();
        }

        // Thực hiện ghi dữ liệu tài khoản User mới vào bảng users
        $sql = "INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email, $password, $role, $status]);

        echo "<script>alert('Đăng ký tài khoản thành công! Hãy đăng nhập hệ thống.'); window.location.href='/AIStudyHub/Guest/frontend/auth/login.php';</script>";
        exit();

    } catch (PDOException $e) {
        die("Lỗi xử lý tạo tài khoản: " . $e->getMessage());
    }
} else {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}