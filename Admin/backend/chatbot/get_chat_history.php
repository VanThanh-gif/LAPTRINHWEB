<?php
require_once __DIR__ . '/../config/connectdb.php';

try {
    $query = "SELECT h.*, u.fullname FROM chatbot_history h 
              JOIN users u ON h.user_id = u.id 
              ORDER BY h.created_at DESC";
    $stmt = $conn->query($query);
    $chats = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Lỗi lấy lịch sử chat: " . $e->getMessage());
}
?>