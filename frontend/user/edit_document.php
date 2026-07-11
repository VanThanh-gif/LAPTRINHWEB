<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';
$id = $_GET['id'] ?? null;
if (!$id) { echo "<script>window.location.href='my_documents.php';</script>"; exit(); }

try {
    $col_id = 'id';
    $check_cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('document_id', $check_cols)) $col_id = 'document_id';
    
    // Check đúng quyền user mới được lấy
    $stmt = $conn->prepare("SELECT * FROM documents WHERE $col_id = ? AND uploaded_by = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $doc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$doc) { echo "<script>alert('Tài liệu không thuộc về bạn!'); window.location.href='my_documents.php';</script>"; exit(); }
} catch (PDOException $e) { die("Lỗi DB."); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Đóng Góp - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .card-custom { background: #ffffff; border: none; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .form-control, .form-select { border-radius: 12px; padding: 14px 18px; font-weight: 500; background-color: #f8fafc; }
        .link-zone { background-color: #f0fdf4; border: 2px dashed #10b981; border-radius: 16px; padding: 25px; }
        .btn-update { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 14px 28px; border-radius: 12px; font-weight: 700; border: none; }
    </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/frontend/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="mb-4 pb-3 border-bottom d-flex justify-content-between">
                    <h4 class="fw-800 text-dark"><i class="bi bi-pencil-square text-success me-2"></i>Sửa Tài Liệu Của Tôi</h4>
                </div>

                <form action="/LAPTRINHWEB/backend/documents/update_document.php" method="POST">
                    <input type="hidden" name="document_id" value="<?= htmlspecialchars($id) ?>">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($doc['title'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Tác giả <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($doc['author'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Chuyên mục <span class="text-danger">*</span></label>
                            <input type="text" name="category_id" class="form-control" value="<?= htmlspecialchars($doc['category_id'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Link Ảnh Bìa</label>
                            <input type="url" name="thumbnail" class="form-control" value="<?= htmlspecialchars($doc['thumbnail'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Mô tả tóm tắt</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($doc['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-5 link-zone">
                        <label class="form-label text-success fs-6 fw-bold"><i class="bi bi-link-45deg fs-4 me-1"></i>Đường Dẫn Tài Liệu (Sửa Link) <span class="text-danger">*</span></label>
                        <input type="url" name="file_path" class="form-control form-control-lg border-success" value="<?= htmlspecialchars($doc['file_path'] ?? '') ?>" required>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="my_documents.php" class="btn btn-light fw-bold py-2 px-4 border">Hủy</a>
                        <button type="submit" class="btn btn-update"><i class="bi bi-check-lg me-2"></i>Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>