<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

try {
    // Lấy toàn bộ tài liệu (Mới nhất lên đầu)
    $stmt = $conn->query("SELECT * FROM documents ORDER BY created_at DESC");
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { die("Lỗi Database: " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Tài Liệu - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-custom { background: #ffffff; border: none; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .table th { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0; }
        .table td { vertical-align: middle; padding: 15px 10px; font-weight: 500; }
        .btn-action { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; transition: 0.2s; border: none; }
    </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php'; ?>

<div class="container-fluid py-5 px-lg-5">
    <div class="card card-custom">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-800 text-dark mb-1"><i class="bi bi-folder-fill text-primary me-2"></i>Quản Lý Tài Liệu</h4>
                <p class="text-muted small mb-0">Duyệt, chỉnh sửa hoặc xóa tài liệu khỏi hệ thống.</p>
            </div>
            <a href="add_document.php" class="btn btn-primary fw-bold rounded-pill px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i> Thêm mới
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="35%">TÊN TÀI LIỆU</th>
                        <th width="20%">THỂ LOẠI</th>
                        <th width="15%" class="text-center">TRẠNG THÁI</th>
                        <th width="25%" class="text-center">THAO TÁC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): 
                        $d_id = $doc['document_id'] ?? $doc['id'] ?? 0;
                        $d_cat = $doc['category_id'] ?? $doc['category'] ?? $doc['file_type'] ?? 'Chưa rõ';
                        $status = $doc['status'] ?? 'approved'; // Mặc định nếu cột rỗng
                    ?>
                    <tr>
                        <td class="text-muted fw-bold">#<?= $d_id ?></td>
                        <td class="text-dark fw-bold"><?= htmlspecialchars($doc['title']) ?></td>
                        
                        <td>
                            <span class="badge bg-light text-primary border border-primary-subtle px-3 py-2 rounded-3">
                                <?= htmlspecialchars($d_cat) ?>
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <?php if ($status === 'pending'): ?>
                                <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill fw-bold">Chờ duyệt</span>
                            <?php elseif ($status === 'rejected'): ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-bold">Từ chối</span>
                            <?php else: ?>
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold">Đã duyệt</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                
                                <?php if ($status === 'pending'): ?>
                                <form action="/LAPTRINHWEB/backend/documents/approve_action.php" method="POST" class="m-0" onsubmit="return confirm('Duyệt tài liệu này lên trang chủ?');">
                                    <input type="hidden" name="document_id" value="<?= $d_id ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-action btn-success text-white" title="Duyệt tài liệu">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <a href="edit_document.php?id=<?= $d_id ?>" class="btn btn-action btn-primary text-white" title="Sửa">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>

                                <form action="/LAPTRINHWEB/backend/documents/delete_document.php" method="POST" class="m-0" onsubmit="return confirm('Xóa vĩnh viễn tài liệu này?');">
                                    <input type="hidden" name="document_id" value="<?= $d_id ?>">
                                    <button type="submit" class="btn btn-action btn-danger text-white" title="Xóa">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>