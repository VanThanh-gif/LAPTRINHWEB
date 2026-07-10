<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Chỉ Admin mới được xóa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Từ chối truy cập!");
}

require_once __DIR__ . '/../../config/connectdb.php';

$doc_id = $_GET['id'] ?? 0;

if ($doc_id > 0) {
    try {
        $stmt = $conn->prepare("DELETE FROM documents WHERE document_id = ?");
        $stmt->execute([$doc_id]);
        
        // Xóa xong đá về Dashboard ngay tại thư mục hiện tại
        echo "<script>alert('Đã xóa tài liệu thành công!'); window.location.href='dashboard.php';</script>";
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Lỗi khi xóa: " . $e->getMessage() . "'); window.location.href='dashboard.php';</script>";
    }
} else {
    header("Location: dashboard.php");
}
?>