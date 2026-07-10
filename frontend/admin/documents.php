<?php
// Bật lỗi để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BẢO MẬT TỐI CAO: Chỉ Admin mới được ở lại trang này
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Lỗi: Khu vực này chỉ dành cho Ban Quản Trị!'); window.location.href='../guest/login.php';</script>";
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

// Lấy toàn bộ danh sách tài liệu từ Database
try {
    $stmt = $conn->prepare("SELECT * FROM documents ORDER BY created_at DESC");
    $stmt->execute();
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $documents = [];
}

// Data ảo dự phòng nếu DB chưa có gì
if (empty($documents)) {
    $documents = [
        ['id' => 1, 'title' => 'Lập trình PHP từ Zero đến Hero', 'author' => 'Tài Nguyễn', 'category' => 'Web Dev', 'created_at' => '2026-06-01'],
        ['id' => 2, 'title' => 'Tài liệu Dựng hình 3D SketchUp & Enscape', 'author' => 'Trần Văn B', 'category' => 'Kiến trúc', 'created_at' => '2026-06-03'],
        ['id' => 3, 'title' => 'Cơ sở dữ liệu & Phân tích hệ thống', 'author' => 'Phạm Thị D', 'category' => 'Database', 'created_at' => '2026-06-07']
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Tài Liệu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        
        .admin-header {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .data-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden; /* Để bảng không bị lòi góc */
        }
        
        /* Chỉnh CSS cho Table nhìn xịn như các Dashboard chuẩn */
        .table { margin-bottom: 0; vertical-align: middle; }
        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 2px solid #e2e8f0;
        }
        .table tbody td {
            padding: 16px 24px;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
        }
        .table tbody tr:hover { background-color: #f8fafc; }
        
        .badge-cat {
            background-color: #ebf5ff;
            color: #2563eb;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .btn-action {
            width: 35px; height: 35px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 8px; border: none; transition: 0.2s;
        }
        .btn-edit { background-color: #fef3c7; color: #d97706; }
        .btn-edit:hover { background-color: #fde68a; color: #b45309; }
        .btn-delete { background-color: #fee2e2; color: #dc2626; }
        .btn-delete:hover { background-color: #fecaca; color: #b91c1c; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="admin-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="bi bi-file-earmark-text text-primary me-2"></i>Quản Lý Tài Liệu</h3>
            <p class="text-muted mb-0 mt-1">Kiểm soát toàn bộ tài liệu trên hệ thống</p>
        </div>
        <a href="add_document.php" class="btn btn-primary fw-bold px-4 py-2 rounded-3 shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm Tài Liệu Mới
        </a>
    </div>
</div>

<div class="container mb-5">
    <div class="data-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">ID</th>
                        <th width="35%">Tên tài liệu</th>
                        <th width="15%">Tác giả</th>
                        <th width="15%">Thể loại</th>
                        <th width="15%">Ngày đăng</th>
                        <th width="15%" class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?= $doc['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($doc['title']) ?></td>
                        <td><?= htmlspecialchars($doc['author'] ?? 'Ẩn danh') ?></td>
                        <td><span class="badge-cat"><?= htmlspecialchars($doc['category'] ?? 'Chưa phân loại') ?></span></td>
                        <td><?= date('d/m/Y', strtotime($doc['created_at'])) ?></td>
                        <td class="text-end">
                            <a href="edit_document.php?id=<?= $doc['id'] ?>" class="btn-action btn-edit me-2" title="Chỉnh sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a href="../../backend/admin/delete_document.php?id=<?= $doc['id'] ?>" class="btn-action btn-delete" title="Xóa tài liệu" onclick="return confirm('Bạn có chắc chắn muốn xóa tài liệu này vĩnh viễn không?');">
                                <i class="bi bi-trash3-fill"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center p-3 border-top">
            <span class="text-muted small">Hiển thị <?= count($documents) ?> tài liệu</span>
            <ul class="pagination pagination-sm m-0">
                <li class="page-item disabled"><a class="page-link" href="#">Trước</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">Sau</a></li>
            </ul>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>