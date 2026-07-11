<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Không tìm thấy tài liệu!'); window.location.href='document.php';</script>";
    exit();
}

try {
    // 1. Dò tìm tên cột ID và lấy thông tin tài liệu
    $cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
    $col_id = 'id';
    if (in_array('document_id', $cols)) $col_id = 'document_id';
    
    $stmt = $conn->prepare("SELECT * FROM documents WHERE $col_id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$doc) {
        echo "<script>alert('Tài liệu không tồn tại hoặc đã bị xóa!'); window.location.href='document.php';</script>";
        exit();
    }

    // 2. Lấy Điểm Đánh Giá Trung Bình
    $rate_stmt = $conn->prepare("SELECT AVG(rating) as avg_rate, COUNT(id) as total_votes FROM document_ratings WHERE document_id = ?");
    $rate_stmt->execute([$id]);
    $rate_data = $rate_stmt->fetch(PDO::FETCH_ASSOC);
    $avg_rate = round($rate_data['avg_rate'] ?? 0, 1);
    $total_votes = $rate_data['total_votes'] ?? 0;

    // 3. Lấy Đánh Giá của User hiện tại (Để hiển thị lại số sao họ đã vote)
    $my_rate = 0;
    if (isset($_SESSION['user_id'])) {
        $my_stmt = $conn->prepare("SELECT rating FROM document_ratings WHERE document_id = ? AND user_id = ?");
        $my_stmt->execute([$id, $_SESSION['user_id']]);
        $my_rate = $my_stmt->fetchColumn() ?: 0;
    }

    // Map dữ liệu cột
    $d_title = $doc['title'] ?? 'Chưa có tiêu đề';
    $d_author = $doc['author'] ?? 'Ẩn danh';
    $d_cat = $doc['category_id'] ?? $doc['category'] ?? $doc['file_type'] ?? 'Khác';
    $d_desc = $doc['description'] ?? 'Không có mô tả cho tài liệu này.';
    $d_file = $doc['file_path'] ?? $doc['document_url'] ?? '#';
    $img_url = !empty($doc['thumbnail']) ? $doc['thumbnail'] : (!empty($doc['image']) ? $doc['image'] : 'https://images.unsplash.com/photo-1456324504439-367cee3b3c32?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'); 

} catch (PDOException $e) { die("Lỗi DB."); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($d_title) ?> - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); background: #fff; }
        .cover-img { width: 100%; height: 350px; object-fit: cover; border-radius: 16px; }
        
        /* CSS Ảo thuật cho Form Đánh Giá 5 Sao */
        .star-rating { direction: rtl; display: inline-block; }
        .star-rating input[type=radio] { display: none; }
        .star-rating label { color: #e2e8f0; font-size: 2.5rem; padding: 0 5px; cursor: pointer; transition: all .2s; }
        .star-rating label:hover, 
        .star-rating label:hover ~ label, 
        .star-rating input[type=radio]:checked ~ label { color: #f59e0b; /* Màu vàng cam */ }
    </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <a href="document.php" class="btn btn-light mb-4 fw-bold text-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left me-2"></i> Quay lại kho tài liệu
            </a>

            <div class="card card-custom p-4 p-md-5">
                <div class="row g-5">
                    <div class="col-md-5 text-center">
                        <img src="<?= htmlspecialchars($img_url) ?>" class="cover-img mb-4 shadow-sm" alt="Cover">
                        <a href="<?= htmlspecialchars($d_file) ?>" target="_blank" class="btn btn-primary w-100 py-3 rounded-pill fw-bold fs-6">
                            <i class="bi bi-book-half me-2"></i> Mở Tài Liệu
                        </a>
                    </div>
                    
                    <div class="col-md-7">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-3">
                            <i class="bi bi-tag-fill me-1"></i> <?= htmlspecialchars($d_cat) ?>
                        </span>
                        
                        <h2 class="fw-bold text-dark mb-3"><?= htmlspecialchars($d_title) ?></h2>
                        
                        <div class="d-flex align-items-center gap-4 mb-4 pb-4 border-bottom">
                            <div class="d-flex align-items-center text-muted fw-medium">
                                <i class="bi bi-person-circle fs-4 me-2 text-primary"></i> <?= htmlspecialchars($d_author) ?>
                            </div>
                            <div class="d-flex align-items-center text-warning fw-bold fs-5">
                                <i class="bi bi-star-fill me-2"></i> <?= $avg_rate ?> <span class="text-muted fs-6 ms-1 fw-normal"> (<?= $total_votes ?> đánh giá)</span>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-2">Mô tả tài liệu:</h6>
                        <p class="text-muted lh-lg mb-5"><?= nl2br(htmlspecialchars($d_desc)) ?></p>

                        <div class="bg-light p-4 rounded-4 text-center">
                            <h5 class="fw-bold mb-1">Bạn thấy tài liệu này thế nào?</h5>
                            <p class="small text-muted mb-3">Đánh giá của bạn giúp cộng đồng tìm được tài liệu tốt.</p>
                            
                            <form action="/LAPTRINHWEB/backend/documents/rate_document.php" method="POST">
                                <input type="hidden" name="document_id" value="<?= htmlspecialchars($id) ?>">
                                
                                <div class="star-rating mb-3">
                                    <input type="radio" id="star5" name="rating" value="5" <?= $my_rate == 5 ? 'checked' : '' ?>><label for="star5" class="bi bi-star-fill" title="5 sao"></label>
                                    <input type="radio" id="star4" name="rating" value="4" <?= $my_rate == 4 ? 'checked' : '' ?>><label for="star4" class="bi bi-star-fill" title="4 sao"></label>
                                    <input type="radio" id="star3" name="rating" value="3" <?= $my_rate == 3 ? 'checked' : '' ?>><label for="star3" class="bi bi-star-fill" title="3 sao"></label>
                                    <input type="radio" id="star2" name="rating" value="2" <?= $my_rate == 2 ? 'checked' : '' ?>><label for="star2" class="bi bi-star-fill" title="2 sao"></label>
                                    <input type="radio" id="star1" name="rating" value="1" <?= $my_rate == 1 ? 'checked' : '' ?>><label for="star1" class="bi bi-star-fill" title="1 sao"></label>
                                </div>
                                
                                <div>
                                    <button type="submit" class="btn btn-warning fw-bold text-dark px-5 rounded-pill shadow-sm">
                                        <?= $my_rate > 0 ? 'Cập Nhật Đánh Giá' : 'Gửi Đánh Giá' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
</body>
</html>