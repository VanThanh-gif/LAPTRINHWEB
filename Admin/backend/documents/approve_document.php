<?php
require_once __DIR__ . '/../config/connectdb.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("UPDATE documents SET status = 'Approved' WHERE id = :id");
        $stmt->execute(['id' => $id]);
    } catch (PDOException $e) {
        die("Lỗi khi duyệt tài liệu: " . $e->getMessage());
    }
}
header("Location: ../../frontend/documents/documents.php");
exit();
?>