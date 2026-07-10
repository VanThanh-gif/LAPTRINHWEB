<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database chung của nhóm
require_once __DIR__ . '/../../config/connectdb.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Chưa đăng nhập']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Lấy lịch sử chat xếp theo thời gian tăng dần
    $stmt = $conn->prepare("SELECT sender, message, created_at FROM chatbot_history WHERE user_id = ? ORDER BY created_at ASC");
    $stmt->execute([$user_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['history' => $history]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Lỗi tải lịch sử: ' . $e->getMessage()]);
}