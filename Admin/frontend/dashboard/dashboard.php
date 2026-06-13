<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}
require_once '../../../config/connectdb.php';
require_once '../../backend/dashboard/statistics.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ Thống Quản Trị - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar { background-color: #1e2833; min-height: 100vh; color: #fff; }
        .sidebar .nav-link { color: #cbd5e1; padding: 12px 20px; border-left: 4px solid transparent; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #2d3d4f; color: #fff; border-left: 4px solid #0d6efd; }
        .stat-card { border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.03); }
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
                <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link" href="../users/users.php"><i class="bi bi-people me-2"></i> Quản lý Thành viên</a></li>
                <li class="nav-item"><a class="nav-link" href="../documents/documents.php"><i class="bi bi-file-earmark-text me-2"></i> Duyệt Tài liệu</a></li>
                <li class="nav-item"><a class="nav-link" href="../chatbot/chats.php"><i class="bi bi-chat-left-dots me-2"></i> Lịch sử Chatbot</a></li>
                <li class="nav-item mt-4">
    <a class="nav-link text-danger border-0" href="/AIStudyHub/Guest/backend/auth/logout.php">
        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
    </a>
</li>
        </div>

        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Bảng điều khiển hệ thống</h3>
                    <small class="text-muted">Cập nhật dữ liệu thời gian thực từ hệ thống CSDL.</small>
                </div>
                <span class="badge bg-dark-subtle text-dark px-3 py-2 border"><i class="bi bi-shield-lock-fill me-1"></i> Quyền: Admin</span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-primary border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-muted small text-uppercase fw-bold mb-1">Tổng sinh viên</p><h3 class="fw-bold mb-0"><?= $total_users ?></h3></div>
                            <div class="bg-primary-subtle text-primary p-3 rounded-circle"><i class="bi bi-people-fill fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-warning border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-muted small text-uppercase fw-bold mb-1">Chờ kiểm duyệt</p><h3 class="fw-bold text-warning mb-0"><?= $pending_docs ?></h3></div>
                            <div class="bg-warning-subtle text-warning p-3 rounded-circle"><i class="bi bi-hourglass-split fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-success border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-muted small text-uppercase fw-bold mb-1">Tài liệu đã duyệt</p><h3 class="fw-bold text-success mb-0"><?= $approved_docs ?></h3></div>
                            <div class="bg-success-subtle text-success p-3 rounded-circle"><i class="bi bi-check-circle-fill fs-4"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card stat-card p-3 border-start border-info border-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><p class="text-muted small text-uppercase fw-bold mb-1">Tổng câu hỏi AI</p><h3 class="fw-bold text-info mb-0"><?= $total_chats ?></h3></div>
                            <div class="bg-info-subtle text-info p-3 rounded-circle"><i class="bi bi-chat-right-text-fill fs-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-success bg-white border" role="alert">
                <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                <strong class="text-success">Kết nối thành công!</strong> Hệ thống đã sẵn sàng điều hướng đồng bộ.
            </div>
        </div>
    </div>
</div>
</body>
</html>