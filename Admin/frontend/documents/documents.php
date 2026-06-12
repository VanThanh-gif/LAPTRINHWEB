<?php require_once '../../backend/documents/get_documents.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Tài liệu - AI Study Hub</title>
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
                <a href="../users/users.php"><i class="bi bi-people me-2"></i> Quản lý Người dùng</a>
                <a href="#" class="active"><i class="bi bi-file-earmark-text me-2"></i> Quản lý Tài liệu</a>
                <a href="../chatbot/chats.php"><i class="bi bi-chat-dots me-2"></i> Lịch sử Chatbot</a>
                <hr class="mx-3 text-secondary">
                <a href="../../../Guest/frontend/auth/login.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-10 p-4">
            <h1 class="h2 pb-2 mb-3 border-bottom">Kiểm duyệt tài liệu sinh viên</h1>
            <div class="card shadow-sm border-0 rounded m-2">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Tiêu đề tài liệu</th>
                                <th>Môn học</th>
                                <th>Người đăng</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($documents)): ?>
                                <tr><td colspan="6" class="text-center py-4 text-muted">Hệ thống chưa ghi nhận tài liệu nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($documents as $doc): ?>
                                    <tr>
                                        <td class="ps-3"><?= $doc['id']; ?></td>
                                        <td><strong><?= htmlspecialchars($doc['title']); ?></strong></td>
                                        <td><span class="badge bg-secondary"><?= strtoupper($doc['subject']); ?></span></td>
                                        <td><?= htmlspecialchars($doc['fullname']); ?></td>
                                        <td>
                                            <?php if ($doc['status'] === 'Approved'): ?>
                                                <span class="badge bg-success">Đã duyệt</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($doc['status'] === 'Pending'): ?>
                                                <a href="../../backend/documents/approve_document.php?id=<?= $doc['id']; ?>" class="btn btn-sm btn-success me-1"><i class="bi bi-check-circle"></i> Duyệt</a>
                                            <?php endif; ?>
                                            <a href="../../backend/documents/delete_document.php?id=<?= $doc['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Xóa vĩnh viễn tài liệu này?')"><i class="bi bi-trash"></i> Xóa</a>
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
</body>
</html>