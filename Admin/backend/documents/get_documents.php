<?php
require_once __DIR__ . '/../config/connectdb.php';

try {
    // Kết hợp bảng (JOIN) với bảng users để lấy tên thật (fullname) của người đăng tài liệu
    $query = "SELECT d.*, u.fullname FROM documents d 
              JOIN users u ON d.user_id = u.id 
              ORDER BY d.status DESC, d.created_at DESC";
    $stmt = $conn->query($query);
    $documents = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi lấy danh sách tài liệu: " . $e->getMessage());
}
?>