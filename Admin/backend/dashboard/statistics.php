<?php
// Đường dẫn: Admin/backend/dashboard/statistics.php
// File này tự động được nhúng vào đầu trang dashboard.php để đếm số liệu

if (!isset($conn)) {
    // Nếu chưa có kết nối, tự động gọi file cấu hình gốc
    require_once __DIR__ . '/../../../config/connectdb.php';
}

try {
    // 1. Đếm tổng số sinh viên (Loại trừ tài khoản admin)
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $total_users = $stmt->fetchColumn();

    // 2. Đếm số file đang chờ duyệt
    $stmt = $conn->query("SELECT COUNT(*) FROM documents WHERE status = 'pending'");
    $pending_docs = $stmt->fetchColumn();

    // 3. Đếm số tài liệu đã được duyệt xuất bản
    $stmt = $conn->query("SELECT COUNT(*) FROM documents WHERE status = 'approved'");
    $approved_docs = $stmt->fetchColumn();

    // 4. Đếm tổng số câu hỏi gửi lên AI
    $stmt = $conn->query("SELECT COUNT(*) FROM chat_history");
    $total_chats = $stmt->fetchColumn();

} catch (PDOException $e) {
    // Ghi nhận lỗi nếu cấu trúc bảng bị sai lệch
    $total_users = 0;
    $pending_docs = 0;
    $approved_docs = 0;
    $total_chats = 0;
}
?>