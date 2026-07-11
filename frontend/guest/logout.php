<?php
// 1. Bật session lên để tìm phiên đăng nhập hiện tại
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Xóa toàn bộ dữ liệu trong Session (ID, User, Avatar, Quyền...)
$_SESSION = array();

// 3. Phá hủy hoàn toàn Session
session_destroy();

// 4. Chuyển hướng người dùng quay lại trang Đăng Nhập
header("Location: login.php");
exit();
?>