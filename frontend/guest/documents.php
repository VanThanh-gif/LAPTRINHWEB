<?php
// Bật lỗi để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Gọi file kết nối Database chung
require_once __DIR__ . '/../../config/connectdb.php';

// Lấy danh sách tài liệu từ Database (Chỉ hiển thị, không cho thao tác sâu)
try {
    $stmt = $conn->prepare("SELECT * FROM documents ORDER BY created_at DESC");
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $documents = [];
}

// Dữ liệu mẫu nếu DB trống
if (empty($documents)) {
    $documents = [
        ['id' => 1, 'title' => 'Lập trình PHP cơ bản', 'author' => 'Nguyễn Văn A', 'category' => 'Lập trình', 'created_at' => '2026-06-01'],
        ['id' => 2, 'title' => 'Lập trình Java nâng cao', 'author' => 'Trần Văn B', 'category' => 'Lập trình', 'created_at' => '2026-06-03'],
        ['id' => 3, 'title' => 'Cơ sở dữ liệu Hệ thống', 'author' => 'Phạm Thị D', 'category' => 'Database', 'created_at' => '2026-06-07']
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khám Phá Tài Liệu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
        .hero-section { background: linear-gradient(135deg, #0f172a 0%, #334155 100%); padding: 80px 0; color: white; text-align: center; margin-bottom: 50px; }
        .hero-title { font-weight: 700; font-size: 2.5rem; margin-bottom: 10px; }
        .doc-card { background: white; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; }
        .doc-card:hover { transform: translateY(-8px); box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1); border-color: #cbd5e1; }
        .doc-title { font-size: 1.15rem; font-weight: 600; color: #0f172a; margin-bottom: 12px; display: flex; align-items: flex-start; gap: 10px; }
        .doc-meta { font-size: 0.9rem; color: #64748b; margin-bottom: 8px; }
        .badge-category { background-color: #f1f5f9; color: #475569; font-weight: 500; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; display: inline-block; margin-bottom: 15px; }
        .btn-guest { background-color: #f8fafc; color: #475569; border: 1px solid #cbd5e1; border-radius: 10px; padding: 10px; font-weight: 600; margin-top: auto; transition: all 0.2s; }
        .btn-guest:hover { background-color: #3b82f6; color: white; border-color: #3b82f6; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="hero-section">
    <div class="container">
        <h1 class="hero-title">Khám Phá Kho Trí Thức</h1>
        <p class="fs-5 text-light opacity-75 mb-4">Hàng ngàn tài liệu học tập đang chờ bạn khám phá</p>
        <a href="register.php" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">Đăng ký thành viên ngay</a>
    </div>
</div>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark m-0">Tài liệu nổi bật</h3>
    </div>

    <div class="row g-4">
        <?php foreach ($documents as $doc): ?>
            <div class="col-md-6 col-lg-4">
                <div class="doc-card">
                    <div class="doc-title">
                        <i class="bi bi-file-earmark-lock2 text-secondary fs-4"></i>
                        <?= htmlspecialchars($doc['title']) ?>
                    </div>
                    
                    <div class="doc-meta">
                        <i class="bi bi-person me-1"></i> Tác giả: <span class="text-dark fw-medium"><?= htmlspecialchars($doc['author'] ?? 'Ẩn danh') ?></span>
                    </div>
                    
                    <div>
                        <span class="badge-category">
                            <i class="bi bi-tag me-1"></i> <?= htmlspecialchars($doc['category'] ?? 'Chưa phân loại') ?>
                        </span>
                    </div>
                    
                    <a href="login.php" onclick="alert('Bạn cần đăng nhập tài khoản để xem chi tiết hoặc tải tài liệu này nhé!');" class="btn btn-guest w-100 text-center text-decoration-none">
                        <i class="bi bi-lock me-1"></i> Đăng nhập để xem
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>