<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

// Kiểm tra bảo mật
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location.href='/LAPTRINHWEB/frontend/guest/login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['document_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category_val = trim($_POST['category_id'] ?? ''); 
    $thumbnail = trim($_POST['thumbnail'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $file_path = trim($_POST['file_path'] ?? '');
    $role = $_SESSION['role'] ?? 'user';

    if ($id && $title && $file_path) {
        try {
            $cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
            $update_parts = [];
            $update_vals = [];

            $mapColumn = function($possible_names, $value) use (&$update_parts, &$update_vals, $cols) {
                foreach ($possible_names as $col_name) {
                    if (in_array($col_name, $cols)) {
                        $update_parts[] = "$col_name = ?";
                        $update_vals[] = $value;
                        return; 
                    }
                }
            };

            // 🚀 ĐÃ BỔ SUNG CỘT 'file_type' VÀO MẢNG DÒ TÌM
            $mapColumn(['title', 'ten_tai_lieu'], $title);
            $mapColumn(['author', 'tac_gia', 'nguoi_dang_tai'], $author);
            $mapColumn(['file_type', 'category_id', 'category', 'the_loai', 'chuyen_muc'], $category_val);
            $mapColumn(['thumbnail', 'anh_bia', 'image', 'hinh_anh'], $thumbnail);
            $mapColumn(['description', 'mo_ta', 'tom_tat'], $description);
            $mapColumn(['file_path', 'document_url', 'link_file', 'duong_dan', 'file'], $file_path);

            $col_id = 'id';
            if (in_array('document_id', $cols)) $col_id = 'document_id';
            elseif (in_array('ma_tl', $cols)) $col_id = 'ma_tl';

            $update_vals[] = $id;

            $sql = "UPDATE documents SET " . implode(', ', $update_parts) . " WHERE $col_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute($update_vals);

            $redirect_url = ($role === 'admin') ? '/LAPTRINHWEB/frontend/admin/documents.php' : '/LAPTRINHWEB/frontend/user/my_documents.php';
            echo "<script>alert('Đã cập nhật tài liệu thành công!'); window.location.href='$redirect_url';</script>";
            exit();

        } catch (PDOException $e) {
            echo "<script>alert('Lỗi Database: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Vui lòng điền đủ Tiêu đề và Link!'); window.history.back();</script>";
    }
}
?>