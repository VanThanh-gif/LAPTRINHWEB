<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connectdb.php';

// Lấy danh sách tài liệu từ Database (chỉ lấy những bài có status không phải là ẩn/xóa nếu cần, ở đây lấy hết)
try {
    $stmt = $conn->prepare("SELECT * FROM documents ORDER BY created_at DESC");
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $documents = [];
}

// Data mẫu nếu rỗng
if (empty($documents)) {
    $documents = [
        [
            'document_id' => 1, 'title' => 'Lập trình PHP từ Zero đến Hero', 
            'author' => 'Tài Nguyễn', 'file_type' => 'Web Dev', 'created_at' => '2026-06-01',
            'image' => 'https://images.unsplash.com/photo-1599507593499-a3f7d7d97667?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'
        ]
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kho Tài Liệu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }
        .hero-section { background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(30, 58, 138, 0.8)), url('https://images.unsplash.com/photo-1513258496099-48168024aec0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat; padding: 80px 0; color: white; text-align: center; margin-bottom: 50px; }
        .doc-card { background: white; border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; }
        .doc-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .card-img-wrapper { position: relative; height: 200px; overflow: hidden; }
        .card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .doc-card:hover .card-img-top { transform: scale(1.08); }
        .category-badge { position: absolute; top: 15px; right: 15px; background: rgba(255, 255, 255, 0.9); color: #1e3a8a; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; backdrop-filter: blur(4px); }
        .card-body { padding: 24px; display: flex; flex-direction: column; }
        .doc-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 15px; line-height: 1.4; }
        .doc-meta { display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.9rem; margin-bottom: 10px; }
        .avatar-sm { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
        .btn-view { background-color: #f1f5f9; color: #3b82f6; border: none; border-radius: 10px; padding: 12px; font-weight: 600; margin-top: auto; transition: all 0.2s; text-decoration: none; }
        .btn-view:hover { background-color: #3b82f6; color: white; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="hero-section">
    <div class="container">
        <h1 class="fw-bold mb-3">Khám Phá Tài Liệu Số</h1>
        <p class="fs-5 text-light opacity-75">Nâng cao kỹ năng với hàng ngàn tài liệu chất lượng cao</p>
    </div>
</div>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 class="fw-bold text-dark m-0">Tài liệu mới nổi bật</h3>
            <p class="text-muted mt-1 mb-0">Cập nhật những kiến thức mới nhất trong tuần</p>
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($documents as $doc): 
            // KHẮC PHỤC LỖI TÊN CỘT: Map đúng tên cột trong DB của nhóm
            $d_id = $doc['document_id'] ?? $doc['id'] ?? 0;
            $d_cat = $doc['file_type'] ?? $doc['category'] ?? 'Khác';
            $d_author = $doc['author'] ?? 'Ẩn danh';
            $d_date = isset($doc['created_at']) ? date('d/m/Y', strtotime($doc['created_at'])) : 'Không rõ';
            
            // Fallback ảnh
            $img_url = !empty($doc['image']) ? $doc['image'] : 'https://images.unsplash.com/photo-1456324504439-367cee3b3c32?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'; 
        ?>
            
            <div class="col-md-6 col-lg-4">
                <div class="doc-card">
                    <div class="card-img-wrapper">
                        <img src="<?= htmlspecialchars($img_url) ?>" class="card-img-top" alt="Cover">
                        <span class="category-badge"><i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($d_cat) ?></span>
                    </div>
                    
                    <div class="card-body">
                        <div class="doc-title">
                            <?= htmlspecialchars($doc['title'] ?? 'Chưa có tiêu đề') ?>
                        </div>
                        
                        <div class="doc-meta">
                            <img src="https://ui-avatars.com/api/?name=<?= urlencode($d_author) ?>&background=random&color=fff" class="avatar-sm" alt="Author">
                            <span class="fw-medium text-dark"><?= htmlspecialchars($d_author) ?></span>
                        </div>
                        
                        <div class="doc-meta mb-4">
                            <i class="bi bi-calendar-event"></i> Ngày đăng: <?= $d_date ?>
                        </div>
                        
                        <a href="document_detail.php?id=<?= $d_id ?>" class="btn btn-view w-100 text-center">
                            Xem chi tiết <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>