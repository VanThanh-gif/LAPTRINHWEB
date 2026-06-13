<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}

// Kết nối database an toàn tuyệt đối bằng DOCUMENT_ROOT
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';

// Xử lý phê duyệt / từ chối tài liệu
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'] === 'approve' ? 'approved' : 'rejected';
    $doc_id = (int)$_GET['id'];
    try {
        $update = $conn->prepare("UPDATE documents SET status = ? WHERE document_id = ?");
        $update->execute([$action, $doc_id]);
    } catch (PDOException $e) { }
    header("Location: documents.php");
    exit();
}

// Lấy danh sách tài liệu và tự động tìm tên cột chứa file (file_path, file, v.v.)
try {
    // Thuật toán dò tìm cột file để tránh lỗi sập truy vấn SQL
    $q_columns = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
    $file_col = 'file_path';
    if (in_array('file_path', $q_columns)) $file_col = 'file_path';
    elseif (in_array('file', $q_columns)) $file_col = 'file';
    elseif (in_array('document_url', $q_columns)) $file_col = 'document_url';
    elseif (in_array('path', $q_columns)) $file_col = 'path';

    $query = "SELECT d.document_id, d.title, d.author, d.status, d.created_at, d.{$file_col} AS file_name, c.category_name 
              FROM documents d 
              JOIN categories c ON d.category_id = c.category_id 
              ORDER BY d.document_id DESC";
    $documents = $conn->query($query)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi lấy danh sách tài liệu: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phê Duyệt Tài Liệu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', system-ui, sans-serif; }
        .sidebar { background-color: #1e2833; min-height: 100vh; color: #fff; }
        .sidebar .nav-link { color: #cbd5e1; padding: 12px 20px; border-left: 4px solid transparent; text-decoration: none; display: block; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background-color: #2d3d4f; color: #fff; border-left: 4px solid #0d6efd; }
        .table-card { background: white; border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.02); }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
            <div class="p-3 text-center border-bottom border-secondary">
                <h5 class="fw-bold mb-0 text-info"><i class="bi bi-cpu-fill me-2"></i>AI Study Hub</h5>
            </div>
            <ul class="nav flex-column mt-3">
                <li class="nav-item"><a class="nav-link" href="../dashboard/dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Tổng quan</a></li>
                <li class="nav-item"><a class="nav-link" href="../users/users.php"><i class="bi bi-people me-2"></i> Quản lý Thành viên</a></li>
                <li class="nav-item"><a class="nav-link active" href="#"><i class="bi bi-file-earmark-text me-2"></i> Duyệt Tài liệu</a></li>
                <li class="nav-item"><a class="nav-link" href="../chatbot/chats.php"><i class="bi bi-chat-left-dots me-2"></i> Lịch sử Chatbot</a></li>
                <li class="nav-item mt-4">
                    <a class="nav-link text-danger border-0" href="/AIStudyHub/Guest/backend/auth/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </li>
            </ul>
        </div>

        <div class="col-md-9 col-lg-10 px-4 py-4">
            <div class="mb-4 pb-2 border-bottom">
                <h3 class="fw-bold text-dark">Kiểm duyệt tài liệu sinh viên</h3>
            </div>
            <div class="card table-card p-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã số</th>
                            <th>Tên giáo trình</th>
                            <th>Chuyên mục</th>
                            <th>Tác giả</th>
                            <th>Tệp tin đính kèm</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($documents)): ?>
                            <tr><td colspan="7" class="text-center text-muted">Chưa có tài liệu nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                            <tr>
                                <td>#<?= $doc['document_id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($doc['title']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($doc['category_name']) ?></span></td>
                                <td><?= htmlspecialchars($doc['author']) ?></td>
                                
                                <td>
                                    <?php if (!empty($doc['file_name'])): ?>
                                        <a href="/AIStudyHub/uploads/<?= $doc['file_name'] ?>" target="_blank" class="btn btn-sm btn-outline-primary px-2 py-1">
                                            <i class="bi bi-eye-fill me-1"></i> Kích vào xem file
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Không có tệp tin</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($doc['status'] === 'approved'): ?>
                                        <span class="badge bg-success">approved</span>
                                    <?php elseif($doc['status'] === 'pending'): ?>
                                        <span class="badge bg-warning text-dark">pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($doc['status'] === 'pending'): ?>
                                        <a href="documents.php?action=approve&id=<?= $doc['document_id'] ?>" class="btn btn-sm btn-success px-3">Duyệt luôn</a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-light border" disabled>Đã duyệt</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>