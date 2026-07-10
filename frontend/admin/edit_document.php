<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../guest/login.php"); exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

$doc_id = $_GET['id'] ?? 0;
$message = ''; $msg_type = '';

// Lấy dữ liệu cũ để điền vào form
try {
    $stmt = $conn->prepare("SELECT * FROM documents WHERE document_id = ?");
    $stmt->execute([$doc_id]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$doc) die("Tài liệu không tồn tại!");
} catch (PDOException $e) { die("Lỗi DB!"); }

// Xử lý Cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $file_path = trim($_POST['file_path']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $status = trim($_POST['status']);

    try {
        $update_sql = "UPDATE documents SET title=?, file_type=?, file_path=?, description=?, image=?, status=? WHERE document_id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([$title, $category, $file_path, $description, $image_url, $status, $doc_id]);
        
        $message = "Đã cập nhật tài liệu thành công!";
        $msg_type = "success";
        
        // Cập nhật lại biến hiển thị
        $doc['title'] = $title; $doc['file_type'] = $category; $doc['file_path'] = $file_path; 
        $doc['description'] = $description; $doc['image'] = $image_url; $doc['status'] = $status;
    } catch (PDOException $e) {
        $message = "Lỗi cập nhật: " . $e->getMessage(); $msg_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Tài Liệu - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold mb-0">Chỉnh sửa tài liệu #<?= $doc_id ?></h4>
                    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Quay lại</a>
                </div>
                
                <?php if ($message): ?>
                    <div class="alert alert-<?= $msg_type ?> rounded-3"><?= $message ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tên tài liệu</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($doc['title']) ?>" required>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Thể loại</label>
                            <input type="text" name="category" class="form-control" value="<?= htmlspecialchars($doc['file_type'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Trạng thái (pending/approved)</label>
                            <input type="text" name="status" class="form-control" value="<?= htmlspecialchars($doc['status'] ?? 'pending') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Ảnh bìa (Link)</label>
                            <input type="text" name="image_url" class="form-control" value="<?= htmlspecialchars($doc['image'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Link Tải File</label>
                        <input type="text" name="file_path" class="form-control" value="<?= htmlspecialchars($doc['file_path'] ?? '') ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($doc['description'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-3">Lưu Thay Đổi</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>