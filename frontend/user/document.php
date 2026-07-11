<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/connectdb.php';

// LẤY DỮ LIỆU TÌM KIẾM TỪ THANH SEARCH
$search = trim($_GET['search'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

try {
    // 🚀 Dò tìm tên cột chuyên mục thực tế trong DB
    $cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
    $cat_col = 'category_id'; // Mặc định
    if (in_array('file_type', $cols)) $cat_col = 'file_type';
    elseif (in_array('category', $cols)) $cat_col = 'category';
    elseif (in_array('the_loai', $cols)) $cat_col = 'the_loai';

    // Xây dựng câu lệnh SQL có điều kiện
    $sql = "SELECT * FROM documents WHERE status = 'approved'";
    $params = [];

    // Nếu người dùng có gõ chữ tìm kiếm
    if (!empty($search)) {
        $sql .= " AND (title LIKE ? OR author LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

   // Nếu người dùng chọn lọc theo chuyên mục
    if (!empty($category_filter)) {
        // Thay vì dùng dấu = cứng ngắc, ta dùng LIKE để tìm kiếm tương đối
        $sql .= " AND $cat_col LIKE ?";
        $params[] = "%$category_filter%";
    }

    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $documents = [];
    echo "<script>console.log('Lỗi SQL: " . addslashes($e->getMessage()) . "');</script>";
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
        .hero-section { background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(30, 58, 138, 0.8)), url('https://images.unsplash.com/photo-1513258496099-48168024aec0?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover no-repeat; padding: 80px 0; color: white; text-align: center; margin-bottom: -50px; padding-bottom: 120px; }
        
        /* CSS cho thanh Search Bar */
        .search-box { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.05); position: relative; z-index: 10; margin-bottom: 50px; }
        .search-input { border-radius: 12px; padding: 14px 20px; background: #f8fafc; border: 1.5px solid #e2e8f0; font-weight: 500; }
        .search-input:focus { background: white; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        .btn-search { background: #3b82f6; border-radius: 12px; padding: 14px; font-weight: bold; transition: 0.3s; }
        .btn-search:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3); }

        .doc-card { background: white; border: none; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); transition: all 0.3s ease; height: 100%; display: flex; flex-direction: column; }
        .doc-card:hover { transform: translateY(-8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .card-img-wrapper { position: relative; height: 200px; overflow: hidden; }
        .card-img-top { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; }
        .doc-card:hover .card-img-top { transform: scale(1.08); }
        .category-badge { position: absolute; top: 15px; right: 15px; background: rgba(255, 255, 255, 0.9); color: #1e3a8a; padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 700; backdrop-filter: blur(4px); z-index: 10;}
        .card-body { padding: 24px; display: flex; flex-direction: column; }
        .doc-title { font-size: 1.15rem; font-weight: 700; color: #0f172a; margin-bottom: 15px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .doc-meta { display: flex; align-items: center; gap: 8px; color: #64748b; font-size: 0.9rem; margin-bottom: 10px; }
        .avatar-sm { width: 24px; height: 24px; border-radius: 50%; object-fit: cover; }
        .btn-view { background-color: #f1f5f9; color: #3b82f6; border: none; border-radius: 10px; padding: 12px; font-weight: 600; margin-top: auto; transition: all 0.2s; text-decoration: none; }
        .btn-view:hover { background-color: #3b82f6; color: white; }
    </style>
</head>
<body>

<?php 
if (file_exists(__DIR__ . '/../../includes/navbar.php')) {
    include __DIR__ . '/../../includes/navbar.php';
}
?>

<div class="hero-section">
    <div class="container">
        <h1 class="fw-bold mb-3">Khám Phá Tài Liệu Số</h1>
        <p class="fs-5 text-light opacity-75">Nâng cao kỹ năng với hàng ngàn tài liệu chất lượng cao</p>
    </div>
</div>

<div class="container mb-5">
    
    <div class="search-box">
        <form action="" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 px-3"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-light" style="padding: 14px;" placeholder="Tìm tên tài liệu, tác giả..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-4">
               <select name="category" class="form-select search-input">
    <option value="">Tất cả chuyên mục</option>
    <option value="Lập trình" <?= $category_filter == 'Lập trình' ? 'selected' : '' ?>>Lập trình Web / Mobile</option>
    <option value="Cơ sở dữ liệu" <?= $category_filter == 'Cơ sở dữ liệu' ? 'selected' : '' ?>>Cơ sở dữ liệu</option>
    <option value="Kiến trúc" <?= $category_filter == 'Kiến trúc' ? 'selected' : '' ?>>Thiết kế / Kiến trúc</option>
    <option value="Ngoại ngữ" <?= $category_filter == 'Ngoại ngữ' ? 'selected' : '' ?>>Ngoại ngữ & Tiếng Anh</option>
    <option value="Sáng tạo nội dung" <?= $category_filter == 'Sáng tạo nội dung' ? 'selected' : '' ?>>Sáng tạo nội dung</option>
    <option value="Khác" <?= $category_filter == 'Khác' ? 'selected' : '' ?>>Khác</option>
</select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-search text-white w-100">Lọc Tài Liệu</button>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
        <div>
            <?php if (!empty($search) || !empty($category_filter)): ?>
                <h4 class="fw-bold text-dark mb-1">Kết quả tìm kiếm</h4>
                <p class="text-muted small mb-0">Tìm thấy <b><?= count($documents) ?></b> tài liệu phù hợp.</p>
            <?php else: ?>
                <h4 class="fw-bold text-dark mb-1">Tài liệu mới nổi bật</h4>
                <p class="text-muted small mb-0">Cập nhật những kiến thức mới nhất trong tuần</p>
            <?php endif; ?>
        </div>
        
        <a href="/LAPTRINHWEB/frontend/user/upload.php" class="btn btn-success fw-bold px-4 py-2 rounded-pill shadow-sm" style="transition: 0.3s;">
            <i class="bi bi-cloud-arrow-up-fill me-2"></i>Đóng góp ngay
        </a>
    </div>

    <div class="row g-4">
        <?php if (empty($documents)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 fw-bold text-dark">Không tìm thấy tài liệu nào!</h5>
                <p class="text-muted">Vui lòng thử lại với từ khóa hoặc chuyên mục khác.</p>
                <a href="document.php" class="btn btn-outline-primary mt-2">Xóa bộ lọc</a>
            </div>
        <?php else: ?>
            <?php foreach ($documents as $doc): 
                $d_id = $doc['document_id'] ?? $doc['id'] ?? 0;
                $d_cat = $doc['category_id'] ?? $doc['category'] ?? $doc['file_type'] ?? 'Khác';
                $d_author = $doc['author'] ?? 'Ẩn danh';
                $d_date = isset($doc['created_at']) ? date('d/m/Y', strtotime($doc['created_at'])) : 'Không rõ';
                
                $img_url = !empty($doc['thumbnail']) ? $doc['thumbnail'] : (!empty($doc['image']) ? $doc['image'] : 'https://images.unsplash.com/photo-1456324504439-367cee3b3c32?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80'); 
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="doc-card">
                        <div class="card-img-wrapper">
                            <img src="<?= htmlspecialchars($img_url) ?>" class="card-img-top" alt="Cover">
                            <span class="category-badge"><i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($d_cat) ?></span>
                        </div>
                        
                        <div class="card-body">
                            <div class="doc-title" title="<?= htmlspecialchars($doc['title'] ?? '') ?>">
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
        <?php endif; ?>
    </div>
</div>

<?php 
// GỌI FOOTER RA TRANG CHỦ Ở ĐÂY SẾP NHÉ
if (file_exists(__DIR__ . '/../../includes/footer.php')) {
    include __DIR__ . '/../../includes/footer.php';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>