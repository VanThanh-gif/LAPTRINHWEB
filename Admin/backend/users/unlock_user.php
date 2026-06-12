<?php
require_once __DIR__ . '/../config/connectdb.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("UPDATE users SET status = 'Active' WHERE id = :id AND role = 'User'");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        die("Lỗi khi mở khóa tài khoản: " . $e->getMessage());
    }
}

header("Location: ../../frontend/users/users.php");
exit();
?>