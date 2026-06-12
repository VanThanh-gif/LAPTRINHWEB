<?php
// Gọi file backend để lấy biến $users chứa danh sách sinh viên
require_once '../../backend/users/get_users.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Người dùng - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; color: white; }
        .sidebar a { color: rgba(255,255,255,0.75); text-decoration: none; display: block; padding: 12px 20px; }
        .sidebar a:hover, .sidebar a.active { background-color: #343a40; color: white; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 px-0 sidebar">
            <div class="p-3 text-center border-bottom border-secondary">
                <h4>AI Study Hub</h4>
                <small class="text-muted">Trang Quản Trị</small>
            </div>
            <div class="py-2">
                <a href="../dashboard/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a>
                <a href="#" class="active"><i class="bi bi-people me-2"></i> Quản lý Người dùng</a>
                <a href="../documents/documents.php"><i class="bi bi-file-earmark-text me-2"></i> Quản lý Tài liệu</a>
                <a href="../chatbot/chats.php"><i class="bi bi-chat-dots me-2"></i> Lịch sử Chatbot</a>
                <hr class="mx-3 text-secondary">
                <a href="../../../Guest/frontend/auth/login.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Quản lý Tài khoản Sinh viên</h1>
            </div>

            <div class="card shadow-sm border-0 rounded m-2">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Tên đăng nhập</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Trạng thái</th>
                                <th>Ngày tham gia</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Chưa có sinh viên nào đăng ký hệ thống.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="ps-3"><?php echo $user['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($user['fullname'] ?? 'Chưa cập nhật'); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <?php if ($user['status'] === 'Active'): ?>
                                                <span class="badge bg-success-subtle text-success px-2 py-1">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger px-2 py-1">Đang khóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td class="text-center">
                                            <?php if ($user['status'] === 'Active'): ?>
                                                <a href="../../backend/users/lock_user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Bạn có chắc chắn muốn KHÓA tài khoản này không?')">
                                                    <i class="bi bi-lock-fill"></i> Khóa
                                                </a>
                                            <?php else: ?>
                                                <a href="../../backend/users/unlock_user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-unlock-fill"></i> Mở khóa
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bundle.min.js"></script>
</body>
</html>