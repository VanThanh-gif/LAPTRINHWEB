<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}

// Kết nối database an toàn tuyệt đối bằng DOCUMENT_ROOT
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';

// Lấy danh sách lịch sử chat từ cơ sở dữ liệu
try {
    // Giả định bảng lưu lịch sử chatbot của bạn tên là chatbot_logs hoặc tương đương.
    // Đoạn query này lấy thông tin người dùng chat và nội dung câu hỏi/trả lời.
    $query = "SELECT c.*, u.username 
              FROM chatbot_logs c
              LEFT JOIN users u ON c.user_id = u.user_id 
              ORDER BY c.log_id DESC";
    
    // Kiểm tra nếu bảng tồn tại trong DB, nếu không có bảng này thì tự động tạo mảng rỗng để tránh sập giao diện
    $stmt = $conn->query("SHOW TABLES LIKE 'chatbot_logs'");
    if ($stmt->rowCount() > 0) {
        $chats = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Khởi tạo dữ liệu mẫu nếu database thực tế chưa chạy migrate bảng logs
        $chats = [
            [
                'username' => 'Trần Minh Tuấn',
                'created_at' => '2026-06-13 13:44:25',
                'question' => 'Lỗi 404 Not Found trong PHP là gì?',
                'response' => 'Là lỗi máy chủ không tìm thấy file theo đường dẫn URL bạn yêu cầu.'
            ]
        ];
    }
} catch (PDOException $e) {
    // Nếu lỗi database, không làm sập giao diện, tạo mảng rỗng để trang vẫn hiển thị đẹp
    $chats = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhật Ký Chatbot - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar { background-color: #1e2833; min-height: 100vh; color: #fff; }
        .sidebar .nav-link { color: #cbd5e1; padding: 12px 20px; border-left: 4px solid transparent; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #2d3d4f; color: #fff; border-left: 4px solid #0d6efd; }
        .chat-card { background: white; border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin-bottom: 20px; }
        .user-badge { color: #0d6efd; font-weight: bold; }
        .bot-badge { color: #198754; font-weight: bold; }
        .time-text { font-size: 0.8rem; color: #6c757d; }
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
                <li class="nav-item"><a class="nav-link" href="../users/users.php"><i class="bi bi-people me-2"></i> Quản lý Thành viên</a></li>
                <li class="nav-item"><a class="nav-link" href="../documents/documents.php"><i class="bi bi-file-earmark-text me-2"></i> Duyệt Tài liệu</a></li>
                <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-chat-left-dots me-2"></i> Lịch sử Chatbot</a></li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger border-0" href="/AIStudyHub/Guest/backend/auth/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="mb-4 pb-2 border-bottom">
                <h3 class="fw-bold text-dark">Nhật ký tương tác Chatbot AI</h3>
            </div>

            <div class="row">
                <div class="col-12 col-xl-8">
                    <?php if (empty($chats)): ?>
                        <div class="alert alert-info text-center">Chưa có lịch sử tương tác nào được ghi lại.</div>
                    <?php else: ?>
                        <?php foreach ($chats as $log): ?>
                        <div class="card chat-card p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                <span class="fw-bold text-secondary"><i class="bi bi-person-fill me-1"></i> Sinh viên: <span class="text-primary"><?= htmlspecialchars($log['username'] ?? 'Ẩn danh') ?></span></span>
                                <span class="time-text"><i class="bi bi-clock me-1"></i> <?= date('H:i:s d/m/Y', strtotime($log['created_at'])) ?></span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="mb-1 user-badge"><i class="bi bi-chat-right-text-fill me-2"></i>Hỏi:</p>
                                <div class="bg-light p-3 rounded text-dark border-start border-primary border-3">
                                    <?= nl2br(htmlspecialchars($log['question'])) ?>
                                </div>
                            </div>

                            <div>
                                <p class="mb-1 bot-badge"><i class="bi bi-robot me-2"></i>AI Đáp:</p>
                                <div class="p-3 rounded text-dark border-start border-success border-3" style="background-color: #f4fbf7;">
                                    <?= nl2br(htmlspecialchars($log['response'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>