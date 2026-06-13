<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}

// Kết nối database an toàn tuyệt đối bằng DOCUMENT_ROOT
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';

// Lấy danh sách thành viên (Sửa lỗi gọi cột id thành user_id)
try {
    $stmt = $conn->query("SELECT user_id, username, email, role, status FROM users ORDER BY user_id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi lấy danh sách người dùng: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Thành viên - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar { background-color: #1e2833; min-height: 100vh; color: #fff; }
        .sidebar .nav-link { color: #cbd5e1; padding: 12px 20px; border-left: 4px solid transparent; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #2d3d4f; color: #fff; border-left: 4px solid #0d6efd; }
        .table-card { background: white; border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
            <div class="p-3 text-center border-bottom border-secondary">
                <h5 class="fw-bold mb-0 text-info"><i class="bi bi-cpu-fill me-2"></i>AI Study Hub</h5>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item"><a class="nav-link" href="../dashboard/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-people me-2"></i> Quản lý Thành viên</a></li>
                <li class="nav-item"><a class="nav-link" href="../documents/documents.php"><i class="bi bi-file-earmark-text me-2"></i> Duyệt Tài liệu</a></li>
                <li class="nav-item"><a class="nav-link" href="../chatbot/chats.php"><i class="bi bi-chat-left-dots me-2"></i> Lịch sử Chatbot</a></li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger border-0" href="/AIStudyHub/Guest/backend/auth/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="mb-4 pb-2 border-bottom">
                <h3 class="fw-bold text-dark">Quản lý Thành viên</h3>
            </div>
            <div class="card table-card p-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã số</th>
                            <th>Họ và tên</th>
                            <th>Email tài khoản</th>
                            <th>Phân quyền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>#<?= $user['user_id'] ?></td>
                            <td><i class="bi bi-person-circle text-primary me-2"></i><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-secondary' ?>">
                                    <?= strtoupper($user['role']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $user['status'] === 'active' ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $user['status'] === 'active' ? 'Hoạt động' : 'Tạm khóa' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>