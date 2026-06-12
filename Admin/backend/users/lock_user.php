<?php
require_once __DIR__ . '/../config/connectdb.php';

// Kiểm tra xem có nhận được ID qua phương thức GET không
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("UPDATE users SET status = 'Locked' WHERE id = :id AND role = 'User'");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        die("Lỗi khi khóa tài khoản: " . $e->getMessage());
    }
}

// Khóa xong thì tự động quay trở lại trang danh sách người dùng bên frontend
header("Location: ../../frontend/users/users.php");
exit();
?>