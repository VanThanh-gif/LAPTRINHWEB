<?php require_once '../../backend/chatbot/get_chat_history.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử Chatbot - AI Study Hub</title>
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
                <a href="../documents/documents.php"><i class="bi bi-file-earmark-text me-2"></i> Quản lý Tài liệu</a>
                <a href="#" class="active"><i class="bi bi-chat-dots me-2"></i> Lịch sử Chatbot</a>
                <hr class="mx-3 text-secondary">
                <a href="../../../Guest/frontend/auth/login.php" class="text-danger"><i class="bi bi-box-arrow-right me-2"></i> Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-10 p-4">
            <h1 class="h2 pb-2 mb-3 border-bottom">Nhật ký hội thoại AI Chatbot</h1>
            <div class="card shadow-sm border-0 rounded m-2">
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3" style="width: 15%;">Sinh viên</th>
                                <th style="width: 35%;">Câu hỏi sinh viên</th>
                                <th style="width: 35%;">AI trả lời</th>
                                <th style="width: 15%;">Thời gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($chats)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Chưa phát sinh phiên hội thoại nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($chats as $chat): ?>
                                    <tr>
                                        <td class="ps-3"><strong><?= htmlspecialchars($chat['fullname']); ?></strong></td>
                                        <td class="text-wrap text-break"><?= htmlspecialchars($chat['user_message']); ?></td>
                                        <td class="text-wrap text-break bg-light text-success"><?= htmlspecialchars($chat['ai_response']); ?></td>
                                        <td><small class="text-muted"><?= date('H:i d/m/Y', strtotime($chat['created_at'])); ?></small></td>
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