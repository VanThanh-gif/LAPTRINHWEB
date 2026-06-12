<?php
// Nhúng file kết nối database (tính từ thư mục users đi ra 2 cấp)
require_once __DIR__ . '/../config/connectdb.php';

try {
    // Lấy danh sách các tài khoản là User (Sinh viên), xếp tài khoản mới lên đầu
    $stmt = $conn->query("SELECT id, username, email, fullname, status, created_at FROM users WHERE role = 'User' ORDER BY id DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi lấy danh sách người dùng: " . $e->getMessage());
}
?>