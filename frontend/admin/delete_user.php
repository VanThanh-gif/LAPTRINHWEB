<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Chỉ Admin mới được quyền xóa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Từ chối truy cập!");
}

require_once __DIR__ . '/../../config/connectdb.php';

$u_id = $_GET['id'] ?? 0;

// Ngăn Admin tự sát hoặc xóa tài khoản ảo 9999
if ($u_id == $_SESSION['user_id'] || $u_id == 9999) {
    echo "<script>alert('Lỗi: Bạn không thể xóa tài khoản Admin đang đăng nhập!'); window.location.href='users.php';</script>";
    exit();
}

if ($u_id > 0) {
    // Tự động quét xem cột ID của nhóm bạn tên là gì (id, user_id, hay ma_tk...)
    $q = $conn->query("DESCRIBE users");
    $cols = $q->fetchAll(PDO::FETCH_COLUMN);
    $id_col = in_array('user_id', $cols) ? 'user_id' : (in_array('id', $cols) ? 'id' : 'ma_tk');

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE $id_col = ?");
        $stmt->execute([$u_id]);
        
        echo "<script>alert('Đã xóa tài khoản thành công!'); window.location.href='users.php';</script>";
    } catch (PDOException $e) {
        // Nếu dính khóa ngoại (User này đã đăng tài liệu nên DB không cho xóa)
        echo "<script>alert('Không thể xóa! Người dùng này đang có tài liệu trên hệ thống. Cần xóa tài liệu của họ trước.'); window.location.href='users.php';</script>";
    }
} else {
    header("Location: users.php");
}
?>