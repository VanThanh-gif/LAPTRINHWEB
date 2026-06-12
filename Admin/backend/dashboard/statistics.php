<?php
// Nhúng file kết nối database bằng đường dẫn tuyệt đối dựa trên thư mục hiện tại của file này
require_once __DIR__ . '/../config/connectdb.php';

try {
    // 1. Đếm tổng số sinh viên (Role là User)
    $stmtUsers = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'User'");
    $total_users = $stmtUsers->fetch()['total'];

    // 2. Đếm số tài liệu đang chờ duyệt (Pending)
    $stmtPending = $conn->query("SELECT COUNT(*) as total FROM documents WHERE status = 'Pending'");
    $pending_docs = $stmtPending->fetch()['total'];

    // 3. Đếm số tài liệu đã duyệt thành công (Approved)
    $stmtApproved = $conn->query("SELECT COUNT(*) as total FROM documents WHERE status = 'Approved'");
    $approved_docs = $stmtApproved->fetch()['total'];

    // 4. Đếm tổng số lượt chat trong hệ thống
    $stmtChats = $conn->query("SELECT COUNT(*) as total FROM chatbot_history");
    $total_chats = $stmtChats->fetch()['total'];

} catch (PDOException $e) {
    die("Lỗi dữ liệu thống kê: " . $e->getMessage());
}
?>