<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

$id =$_GET['id'] ?? null;
if (!$id) {
    echo "<script>alert('Lỗi: Thiếu ID tài liệu!'); window.location.href='documents.php';</script>";
    exit();
}

try {
    $cols =$conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
    $col_id = 'id';
    if (in_array('document_id', $cols))$col_id = 'document_id';
    
    $stmt =$conn->prepare("SELECT * FROM documents WHERE $col_id = ?");
    $stmt->execute([$id]);
    $doc =$stmt->fetch(PDO::FETCH_ASSOC);
    if (!$doc) {
        echo "<script>alert('Không tìm thấy tài liệu!'); window.location.href='documents.php';</script>";
        exit();
    }

    // 🚀 TUYỆT CHIÊU TỰ ĐỘNG MAP DỮ LIỆU CHỐNG LỖI TRỐNG Ô
    $d_cat = $doc['category_id'] ?? $doc['category'] ?? $doc['file_type'] ?? $doc['the_loai'] ?? '';
    $d_thumb =$doc['thumbnail'] ?? $doc['image'] ?? $doc['anh_bia'] ?? '';
    $d_file =$doc['file_path'] ?? $doc['document_url'] ?? $doc['file'] ?? '';

} catch (PDOException $e) { die("Lỗi DB."); }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Tài Liệu - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .card-custom { background: #ffffff; border: none; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); }
        .form-control, .form-select { border-radius: 12px; padding: 14px 18px; font-weight: 500; background-color: #f8fafc; }
        .link-zone { background-color: #fff9e6; border: 2px dashed #fcd34d; border-radius: 16px; padding: 25px; }
        .btn-update { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 14px 28px; border-radius: 12px; font-weight: 700; border: none; }
    </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom">
                <div class="mb-4 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="fw-800 text-dark mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Cập Nhật Tài Liệu</h4>
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">Admin Edit</span>
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
    <select name="category_id" class="form-select" required>
        <option value="" disabled>-- Chọn chuyên mục --</option>
        <option value="Lập trình Web / Mobile" <?= ($d_cat == 'Lập trình Web / Mobile' || $d_cat == 'Web') ? 'selected' : '' ?>>Lập trình Web / Mobile</option>
        <option value="Cơ sở dữ liệu" <?= ($d_cat == 'Cơ sở dữ liệu' || $d_cat == 'Database') ? 'selected' : '' ?>>Cơ sở dữ liệu</option>
        <option value="Thiết kế / Kiến trúc" <?= ($d_cat == 'Thiết kế / Kiến trúc' || $d_cat == 'Kiến trúc') ? 'selected' : '' ?>>Thiết kế / Kiến trúc</option>
        <option value="Ngoại ngữ & Tiếng Anh" <?= ($d_cat == 'Ngoại ngữ & Tiếng Anh' || $d_cat == 'Tiếng Anh') ? 'selected' : '' ?>>Ngoại ngữ & Tiếng Anh</option>
        <option value="Sáng tạo nội dung" <?= ($d_cat == 'Sáng tạo nội dung') ? 'selected' : '' ?>>Sáng tạo nội dung</option>
        <option value="Khác" <?= ($d_cat == 'Khác') ? 'selected' : '' ?>>Khác</option>
    </select>
</div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Link Ảnh Bìa</label>
                            <input type="url" name="thumbnail" class="form-control" value="<?= htmlspecialchars($d_thumb) ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small">Mô tả tóm tắt</label>
                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($doc['description'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-5 link-zone">
                        <label class="form-label text-warning fs-6 fw-bold"><i class="bi bi-link-45deg fs-4 me-1"></i>Đường Dẫn Tài Liệu (Sửa Link) <span class="text-danger">*</span></label>
                        <input type="url" name="file_path" class="form-control form-control-lg border-warning" value="<?= htmlspecialchars($d_file) ?>" required>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="documents.php" class="btn btn-light fw-bold py-2 px-4 border">Hủy</a>
                        <button type="submit" class="btn btn-update"><i class="bi bi-save-fill me-2"></i>Lưu Thay Đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>