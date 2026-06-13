<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Xóa sạch tất cả các biến Session
$_SESSION = array();

// 2. Xóa Cookie lưu Session (nếu có) để bảo mật
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hủy phiên làm việc hoàn toàn
session_destroy();

// 4. Chuyển hướng về trang đăng nhập (Dùng đường dẫn tuyệt đối)
header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
exit();