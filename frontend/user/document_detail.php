<?php
// Bật lỗi để debug (Nhớ tắt khi nộp đồ án nhé)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chặn người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: ../guest/login.php");
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

// 1. Lấy ID tài liệu từ thanh URL
$doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$document = null;

// 2. Lấy dữ liệu từ Database (Khớp 100% với cột document_id của nhóm bạn)
if ($doc_id > 0) {
    try {
        $stmt = $conn->prepare("SELECT * FROM documents WHERE document_id = ?");
        $stmt->execute([$doc_id]);
        $document = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Lỗi kết nối
    }
}

// 3. Đá về trang chủ nếu tài liệu không tồn tại
if (!$document) {
    echo "<script>alert('Tài liệu không tồn tại hoặc đã bị xóa!'); window.location.href='document.php';</script>";
    exit();
}

// 4. Map chuẩn các trường dữ liệu theo CSDL
$d_title = $document['title'] ?? 'Chưa có tiêu đề';
$d_cat = $document['file_type'] ?? 'Chưa phân loại';
$d_author = $document['author'] ?? 'Ẩn danh';
$d_desc = $document['description'] ?? 'Chưa có mô tả cho tài liệu này.';
$d_date = isset($document['created_at']) ? date('d/m/Y', strtotime($document['created_at'])) : 'Không rõ';

// Xử lý File và Ảnh
$img_url = !empty($document['image']) ? $document['image'] : 'https://images.unsplash.com/photo-1456324504439-367cee3b3c32?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80';
$file_path = $document['file_path'] ?? '#';
$is_valid_file = (!empty($file_path) && $file_path !== '#');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($d_title) ?> - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        
        .breadcrumb-wrap { padding: 20px 0; background: white; border-bottom: 1px solid #e2e8f0; margin-bottom: 40px; }
        .breadcrumb a { color: #64748b; text-decoration: none; font-weight: 500; }
        .breadcrumb a:hover { color: #2563eb; }
        
        .cover-image {
            width: 100%; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            object-fit: cover; aspect-ratio: 4/3;
        }
        
        .doc-title { font-size: 2rem; font-weight: 800; color: #0f172a; line-height: 1.3; }
        .doc-author { font-size: 1.1rem; color: #475569; font-weight: 500; }
        .badge-category { background-color: #ebf5ff; color: #2563eb; font-weight: 700; padding: 8px 16px; border-radius: 8px; font-size: 0.9rem; }
        
        .desc-box { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-top: 30px; line-height: 1.8; color: #334155; }
        
        .btn-action { padding: 14px 24px; font-weight: 700; border-radius: 12px; transition: all 0.3s; }
        .btn-download { background-color: #2563eb; color: white; border: none; box-shadow: 0 4px 15px rgba(37,99,235,0.3); }
        .btn-download:hover { background-color: #1d4ed8; transform: translateY(-2px); color: white; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="breadcrumb-wrap">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="document.php"><i class="bi bi-house-door"></i> Kho Tài Liệu</a></li>
                <li class="breadcrumb-item active fw-bold" aria-current="page">Chi tiết tài liệu</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <div class="col-lg-4">
            <img src="<?= htmlspecialchars($img_url) ?>" class="cover-image mb-4" alt="Cover">
            
            <div class="d-grid gap-3">
                
                <?php if ($is_valid_file): ?>
                    <a href="<?= htmlspecialchars($file_path) ?>" target="_blank" download class="btn btn-action btn-download text-decoration-none text-center">
                        <i class="bi bi-cloud-arrow-down-fill me-2"></i> Tải Tài Liệu Về Máy
                    </a>
                <?php else: ?>
                    <button class="btn btn-action btn-download" onclick="alert('Tác giả chưa cập nhật file đính kèm cho tài liệu này!');">
                        <i class="bi bi-cloud-arrow-down-fill me-2"></i> Tải Tài Liệu Về Máy
                    </button>
                <?php endif; ?>

                <button id="btnFavorite" class="btn btn-action btn-outline-secondary bg-white" onclick="toggleFavorite()">
                    <i id="favIcon" class="bi bi-bookmark-plus me-2"></i> <span id="favText">Lưu vào Yêu thích</span>
                </button>
                
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="badge-category"><i class="bi bi-folder2-open me-1"></i> <?= htmlspecialchars($d_cat) ?></span>
                <span class="text-muted"><i class="bi bi-calendar3 me-1"></i> Đăng ngày: <?= $d_date ?></span>
            </div>
            
            <h1 class="doc-title mb-3"><?= htmlspecialchars($d_title) ?></h1>
            
            <div class="d-flex align-items-center gap-2 mb-4 doc-author border-bottom pb-4">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($d_author) ?>&background=random&color=fff" class="rounded-circle" width="35" height="35" alt="Author">
                Biên soạn bởi: <strong class="text-dark"><?= htmlspecialchars($d_author) ?></strong>
            </div>

            <div class="desc-box">
                <h5 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>Mô tả tài liệu</h5>
                <p class="mb-0">
                    <?= nl2br(htmlspecialchars($d_desc)) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Hàm tạo hiệu ứng cho nút Lưu Yêu Thích
    function toggleFavorite() {
        const btn = document.getElementById('btnFavorite');
        const icon = document.getElementById('favIcon');
        const text = document.getElementById('favText');

        if (text.innerText === 'Lưu vào Yêu thích') {
            icon.className = 'bi bi-bookmark-check-fill text-success me-2';
            text.innerText = 'Đã lưu yêu thích';
            btn.classList.add('border-success', 'text-success');
            
            alert('💚 Đã thêm vào danh sách Tài liệu Yêu thích!');
        } else {
            icon.className = 'bi bi-bookmark-plus me-2';
            text.innerText = 'Lưu vào Yêu thích';
            btn.classList.remove('border-success', 'text-success');
        }
    }
</script>
</body>
</html>