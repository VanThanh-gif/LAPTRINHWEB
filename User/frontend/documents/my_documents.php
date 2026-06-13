<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role'])) {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}
require_once '../../../config/connectdb.php';

try {
    // Chỉ tải ra những tài liệu đã được Admin phê duyệt trạng thái 'approved' lên bảng tin công khai
    $query = "SELECT d.*, c.category_name FROM documents d 
              JOIN categories c ON d.category_id = c.category_id 
              WHERE d.status = 'approved' ORDER BY d.document_id DESC";
    $documents = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi hiển thị kho bài học: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kho Tài Liệu Học Tập - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        .hero-banner { background: linear-gradient(135deg, #1d4ed8, #3b82f6); color: white; padding: 60px 0; text-center: center; }
        .card-document { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s; background: #fff; }
        .card-document:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
        .btn-blue { background-color: #2563eb; color: white; border-radius: 8px; width: 100%; transition: 0.2s; }
        .btn-blue:hover { background-color: #1d4ed8; color: white; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold text-info" href="#"><i class="bi bi-folder-symlink-fill me-2"></i>Document Sharing</a>
        <div class="d-flex align-items-center">
            <span class="text-light me-3 small"><i class="bi bi-person-fill text-info me-1"></i>Chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Sinh viên') ?></span>
            <a href="upload.php" class="btn btn-sm btn-outline-success me-2"><i class="bi bi-cloud-arrow-up-fill"></i> Đăng tài liệu mới</a>
            <a href="../../../Guest/backend/auth/logout.php" class="btn btn-sm btn-danger"><i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</nav>

<div class="hero-banner text-center">
    <div class="container">
        <h1 class="fw-bold mb-3">Kho Tài Liệu Học Tập</h1>
        <p class="lead mb-4">Chia sẻ và tìm kiếm tài liệu học tập dễ dàng</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-content form-control py-2 ps-3" placeholder="Tìm kiếm tài liệu học tập giảng đường...">
                    <button class="btn btn-warning px-4"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark mb-0">Danh sách tài liệu</h3>
        <span class="badge bg-primary px-3 py-2 fs-6"><?= count($documents) ?> tài liệu hiện có</span>
    </div>

    <div class="row g-4">
        <?php foreach ($documents as $doc): ?>
        <div class="col-md-4">
            <div class="card card-document p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="p-3 bg-light rounded text-secondary me-3"><i class="bi bi-file-earmark-text fs-3"></i></div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($doc['title']) ?></h5>
                        <small class="text-muted">Tác giả: <?= htmlspecialchars($doc['author']) ?></small>
                    </div>
                </div>
                <p class="text-muted small mb-3 text-truncate-2" style="height: 42px; overflow: hidden;"><?= htmlspecialchars($doc['description'] ?? 'Chưa có mô tả ngắn cụ thể cho tài liệu này.') ?></p>
                <div class="mb-3">
                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1"><?= htmlspecialchars($doc['category_name']) ?></span>
                    <small class="text-muted ms-2"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($doc['created_at'])) ?></small>
                </div>
                <a href="#" class="btn btn-blue py-2 fw-semibold">Xem chi tiết</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>