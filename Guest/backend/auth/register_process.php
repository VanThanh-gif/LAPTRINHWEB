<?php
session_start();
// Đi ngược 3 cấp thư mục để vào Admin/backend/config/connectdb.php
require_once '../../../Admin/backend/config/connectdb.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
        header("Location: ../../frontend/auth/register.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Email này đã được sử dụng!";
            header("Location: ../../frontend/auth/register.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $insertStmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'user', 'active')");
        $insertStmt->execute([$username, $email, $hashed_password]);

        $_SESSION['success'] = "Đăng ký thành công! Hãy đăng nhập.";
        header("Location: ../../frontend/auth/login.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
        header("Location: ../../frontend/auth/register.php");
        exit();
    }
}
?>