<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Lỗi: Khu vực này chỉ dành cho Ban Quản Trị!'); window.location.href='../guest/login.php';</script>";
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

// Lấy danh sách thành viên từ Database
try {
    $stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4f7fe; color: #2b3674; }
        .admin-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 20px 0; margin-bottom: 30px; }
        .data-card { background: #ffffff; border-radius: 24px; box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); border: none; overflow: hidden; padding: 20px; }
        
        .table-custom { vertical-align: middle; }
        .table-custom thead th { background-color: transparent; color: #a3aed1; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-bottom: 2px solid #f4f7fe; }
        .table-custom tbody td { padding: 18px 15px; color: #2b3674; border-bottom: 1px solid #f4f7fe; font-weight: 500; }
        .table-custom tbody tr:hover { background-color: #f8faff; }
        
        .badge-role-admin { background-color: #fee2e2; color: #ef4444; padding: 6px 12px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; }
        .badge-role-user { background-color: #d1e7dd; color: #0f5132; padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.75rem; }

        .btn-action { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; border: none; transition: 0.2s; text-decoration: none; }
        .btn-edit { background-color: #f4f7fe; color: #4318FF; }
        .btn-edit:hover { background-color: #4318FF; color: white; }
        .btn-delete { background-color: #fee2e2; color: #ef4444; }
        .btn-delete:hover { background-color: #ef4444; color: white; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="admin-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="bi bi-people-fill text-primary me-2"></i>Quản Lý Người Dùng</h3>
            <p class="text-muted mb-0 mt-1">Danh sách thành viên hệ thống</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary fw-bold px-4 py-2 rounded-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Trở về Dashboard
        </a>
    </div>
</div>

<div class="container mb-5">
    <div class="data-card">
        <div class="table-responsive">
            <table class="table table-custom mb-0">
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="25%">Họ và tên</th>
                        <th width="25%">Email</th>
                        <th width="15%">Quyền hạn</th>
                        <th width="15%">Ngày tham gia</th>
                        <th width="10%" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): 
                        // Đồng bộ chuẩn cột user_id của nhóm Tài
                        $u_id = $u['user_id'] ?? $u['id'] ?? 0;
                        $u_name = $u['username'] ?? 'Chưa cập nhật';
                        $u_date = isset($u['created_at']) ? date('d/m/Y', strtotime($u['created_at'])) : 'Không rõ';
                        $u_role = $u['role'] ?? $u['quyen'] ?? 'user';
                    ?>
                    <tr>
                        <td class="text-secondary">#<?= htmlspecialchars($u_id) ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($u_name) ?>&background=random&color=fff" class="rounded-circle me-2" width="30" height="30">
                                <span class="fw-bold"><?= htmlspecialchars($u_name) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                        <td>
                            <?php if ($u_role === 'admin'): ?>
                                <span class="badge-role-admin"><i class="bi bi-shield-lock-fill me-1"></i> Admin</span>
                            <?php else: ?>
                                <span class="badge-role-user"><i class="bi bi-person-fill me-1"></i> User</span>
                            <?php endif; ?>
                        </td>
                        <td><?= $u_date ?></td>
                        <td class="text-end">
                            <a href="edit_user.php?id=<?= htmlspecialchars($u_id) ?>" class="btn-action btn-edit me-1" title="Sửa">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            
                            <?php if ($u_id != $_SESSION['user_id'] && $u_id != 9999): ?>
                                <a href="delete_user.php?id=<?= htmlspecialchars($u_id) ?>" class="btn-action btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc chắn muốn xóa user này không?');">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn-action bg-light text-muted" title="Không thể xóa chính mình" disabled><i class="bi bi-slash-circle"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>