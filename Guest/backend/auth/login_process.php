<?php
session_start();
require_once '../../../Admin/backend/config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'locked') {
                $_SESSION['error'] = "Tài khoản của bạn đã bị khóa!";
                header("Location: ../../frontend/auth/login.php");
                exit();
            }

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../../../Admin/frontend/dashboard/dashboard.php");
            } else {
                header("Location: ../../../User/frontend/documents/home.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Email hoặc Mật khẩu không chính xác!";
            header("Location: ../../frontend/auth/login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi hệ thống: " . $e->getMessage();
        header("Location: ../../frontend/auth/login.php");
        exit();
    }
}
?>