<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

// Bảo mật: Chỉ admin mới có quyền duyệt
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Quyền truy cập bị từ chối!'); window.location.href='/LAPTRINHWEB/frontend/guest/login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['document_id'] ?? null;
    $action = $_POST['action'] ?? null;

    if ($id && $action) {
        // Chuyển đổi lệnh thành trạng thái (approved hoặc rejected)
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $msg = ($action === 'approve') ? 'Đã phê duyệt và xuất bản tài liệu thành công!' : 'Đã từ chối tài liệu.';

        try {
            // Tự động quét tìm tên cột khóa chính (ID) trong Database của sếp
            $cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
            $col_id = 'id';
            if (in_array('document_id', $cols)) $col_id = 'document_id';
            elseif (in_array('ma_tl', $cols)) $col_id = 'ma_tl';

            // Cập nhật trạng thái
            $sql = "UPDATE documents SET status = ? WHERE $col_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$status, $id]);

            // Trở về trang Quản lý tài liệu của Admin
            echo "<script>alert('$msg'); window.location.href='/LAPTRINHWEB/frontend/admin/documents.php';</script>";
            exit();

        } catch (PDOException $e) {
            echo "<script>alert('Lỗi Database: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Dữ liệu yêu cầu không hợp lệ!'); window.history.back();</script>";
    }
} else {
    header("Location: /LAPTRINHWEB/frontend/admin/documents.php");
    exit();
}
?>