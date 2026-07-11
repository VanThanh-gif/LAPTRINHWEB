<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập để đánh giá tài liệu!'); window.history.back();</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $doc_id = $_POST['document_id'] ?? 0;
    $rating = (int)($_POST['rating'] ?? 0);

    if ($doc_id && $rating >= 1 && $rating <= 5) {
        try {
            // Kiểm tra xem user này đã từng đánh giá tài liệu này chưa
            $check = $conn->prepare("SELECT id FROM document_ratings WHERE document_id = ? AND user_id = ?");
            $check->execute([$doc_id, $user_id]);

            if ($check->rowCount() > 0) {
                // Đã đánh giá -> Cập nhật lại số sao mới
                $stmt = $conn->prepare("UPDATE document_ratings SET rating = ? WHERE document_id = ? AND user_id = ?");
                $stmt->execute([$rating, $doc_id, $user_id]);
                $msg = "Đã cập nhật lại đánh giá của bạn thành $rating sao!";
            } else {
                // Chưa đánh giá -> Thêm mới
                $stmt = $conn->prepare("INSERT INTO document_ratings (document_id, user_id, rating) VALUES (?, ?, ?)");
                $stmt->execute([$doc_id, $user_id, $rating]);
                $msg = "Cảm ơn bạn đã đánh giá tài liệu $rating sao!";
            }
            
            // Quay lại trang chi tiết tài liệu
            echo "<script>alert('$msg'); window.location.href='/LAPTRINHWEB/frontend/user/document_detail.php?id=$doc_id';</script>";
            exit();

        } catch (PDOException $e) {
            echo "<script>alert('Lỗi Database: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Vui lòng chọn số sao hợp lệ!'); window.history.back();</script>";
    }
}
?>