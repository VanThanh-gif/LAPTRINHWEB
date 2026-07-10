<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../guest/login.php"); exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

$u_id = $_GET['id'] ?? 0;
$message = ''; $msg_type = '';

// Tự động nhận diện tên cột trong Database
$q = $conn->query("DESCRIBE users");
$cols = $q->fetchAll(PDO::FETCH_COLUMN);
$id_col = in_array('user_id', $cols) ? 'user_id' : (in_array('id', $cols) ? 'id' : 'ma_tk');
$role_col = in_array('role', $cols) ? 'role' : (in_array('quyen', $cols) ? 'quyen' : 'role');

// Lấy thông tin user hiện tại
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE $id_col = ?");
    $stmt->execute([$u_id]);
    $user_edit = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user_edit) die("Người dùng không tồn tại!");
} catch (PDOException $e) { die("Lỗi kết nối CSDL!"); }

// Xử lý khi bấm nút Cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    try {
        $update_sql = "UPDATE users SET username=?, email=?, $role_col=? WHERE $id_col=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([$username, $email, $role, $u_id]);

        $message = "Đã cập nhật thông tin tài khoản thành công!";
        $msg_type = "success";
        
        // Cập nhật biến để hiển thị ngay lập tức
        $user_edit['username'] = $username;
        $user_edit['email'] = $email;
        $user_edit[$role_col] = $role;
    } catch (PDOException $e) {
        $message = "Lỗi cập nhật (Có thể Email bị trùng): " . $e->getMessage(); 
        $msg_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân Quyền Người Dùng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }</style>
</head>
<body>
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Quản lý Tài khoản #<?= htmlspecialchars($u_id) ?></h4>
                    <a href="users.php" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?= $msg_type ?> rounded-3"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Họ và tên</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user_edit['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user_edit['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Cấp quyền hạn (Role)</label>
                        <select name="role" class="form-select">
                            <option value="user" <?= (($user_edit[$role_col] ?? 'user') === 'user') ? 'selected' : '' ?>>🟢 Người dùng thường (Sinh viên)</option>
                            <option value="admin" <?= (($user_edit[$role_col] ?? 'user') === 'admin') ? 'selected' : '' ?>>🔴 Quản trị viên (Admin)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3">Cập Nhật Tài Khoản</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>