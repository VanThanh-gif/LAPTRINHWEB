<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';
$user_id = $_SESSION['user_id'];

try {
    // Dò cột để lấy tài liệu của đúng user đang đăng nhập
    $cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
    $col_user = in_array('uploaded_by', $cols) ? 'uploaded_by' : 'user_id';
    
    $sql = "SELECT * FROM documents WHERE $col_user = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id]);
    $my_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Lỗi DB."); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài liệu của tôi - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-custom { background: #ffffff; border: none; border-radius: 20px; padding: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="card card-custom">
        <h4 class="fw-800 text-dark mb-4 border-bottom pb-3"><i class="bi bi-folder-symlink-fill text-success me-2"></i>Tài Liệu Đã Đóng Góp</h4>
        
        <?php if (empty($my_docs)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-folder-x fs-1"></i>
                <p class="mt-2 fw-semibold">Bạn chưa có đóng góp tài liệu nào lên hệ thống.</p>
                <a href="upload.php" class="btn btn-success btn-sm mt-1 rounded-3">Đóng góp ngay</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-secondary small fw-bold">
                            <th>TIÊU ĐỀ TÀI LIỆU</th>
                            <th>CHUYÊN MỤC</th>
                            <th>NGÀY GỬI</th>
                            <th>TRẠNG THÁI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($my_docs as $doc): ?>
                            <tr>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($doc['title']) ?></td>
                                <td><?= htmlspecialchars($doc['category_id'] ?? $doc['category'] ?? 'Chưa rõ') ?></td>
                                <td class="small text-muted"><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                                <td>
                                    <?php if ($doc['status'] === 'approved'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold"><i class="bi bi-check-circle-fill me-1"></i> Đã Duyệt</span>
                                    <?php elseif ($doc['status'] === 'rejected'): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold"><i class="bi bi-x-circle-fill me-1"></i> Từ Chối</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-bold"><i class="bi bi-hourglass-split me-1"></i> Chờ Duyệt</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>