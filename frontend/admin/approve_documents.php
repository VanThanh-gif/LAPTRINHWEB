<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

try {
    // Lấy danh sách tài liệu đang chờ duyệt (kèm tên người gửi nếu có)
    $sql = "SELECT d.*, u.username FROM documents d 
            LEFT JOIN users u ON d.uploaded_by = u.user_id OR d.user_id = u.user_id
            WHERE d.status = 'pending' ORDER BY d.created_at DESC";
    $stmt = $conn->query($sql);
    $pending_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi Database: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Duyệt Tài Liệu - Ban Quản Trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .card-custom { background: #ffffff; border: none; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .table-custom th { background-color: #f8fafc; color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #edf2f7; }
        .table-custom td { padding: 15px; vertical-align: middle; font-size: 0.9rem; font-weight: 500; }
        .btn-action { padding: 8px 16px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; transition: 0.2s; }
    </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php'; ?>

<div class="container-fluid py-5 px-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-custom">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-800 text-dark mb-1"><i class="bi bi-shield-check text-primary me-2"></i>Phê Duyệt Tài Liệu</h4>
                        <p class="text-muted small mb-0">Xem xét và kích hoạt các tài liệu do người dùng đóng góp lên hệ thống.</p>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-hourglass-split me-1"></i> Đang chờ: <?= count($pending_docs) ?>
                    </span>
                </div>

                <?php if (empty($pending_docs)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check2-circle text-success" style="font-size: 4rem;"></i>
                        <h5 class="fw-bold mt-3">Sạch sẽ! Không có tài liệu nào chờ duyệt</h5>
                        <p class="text-muted small">Hệ thống đang hoạt động rất tốt.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-custom table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tên tài liệu</th>
                                    <th>Tác giả</th>
                                    <th>Chuyên mục</th>
                                    <th>Người đăng</th>
                                    <th>Đường liên kết</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_docs as $doc): 
                                    // Tìm trường ID linh hoạt
                                    $doc_id = $doc['document_id'] ?? $doc['id'] ?? null;
                                ?>
                                    <tr>
                                        <td class="fw-bold text-dark"><?= htmlspecialchars($doc['title']) ?></td>
                                        <td><?= htmlspecialchars($doc['author']) ?></td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-secondary fw-semibold"><?= htmlspecialchars($doc['category_id'] ?? $doc['category'] ?? 'Chưa rõ') ?></span></td>
                                        <td class="text-primary fw-semibold"><?= htmlspecialchars($doc['username'] ?? 'Sinh viên') ?></td>
                                        <td>
                                            <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-sm btn-link text-decoration-none fw-bold">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Xem File
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="/LAPTRINHWEB/backend/documents/approve_action.php" method="POST" onsubmit="return confirm('Xác nhận duyệt tài liệu này?');">
                                                    <input type="hidden" name="document_id" value="<?= $doc_id ?>">
                                                    <input type="hidden" name="action" value="approve">
                                                    <button type="submit" class="btn btn-action btn-success"><i class="bi bi-check-lg me-1"></i> Duyệt</button>
                                                </form>
                                                <form action="/LAPTRINHWEB/backend/documents/approve_action.php" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn từ chối tài liệu này?');">
                                                    <input type="hidden" name="document_id" value="<?= $doc_id ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-action btn-danger"><i class="bi bi-x-lg me-1"></i> Từ chối</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>