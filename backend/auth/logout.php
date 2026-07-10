<?php
// Khởi động session để nhận diện người dùng hiện tại
session_start();

// Xóa toàn bộ các biến trong session (user_id, username, role...)
$_SESSION = array();

// Nếu hệ thống của nhóm bạn có dùng Cookie để ghi nhớ đăng nhập, thì hủy luôn Cookie đó
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Phá hủy hoàn toàn phiên làm việc
session_destroy();

// Điều hướng mượt mà về lại trang Đăng nhập dành cho Khách
header("Location: ../../frontend/guest/login.php");
exit();
?>