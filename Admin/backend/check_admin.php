<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Nếu chưa đăng nhập hoặc đăng nhập rồi nhưng không phải Admin -> Đuổi về trang login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../../Guest/frontend/auth/login.php");
    exit();
}
?>