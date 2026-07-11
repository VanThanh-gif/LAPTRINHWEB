<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Kiểm tra bảo mật: Phải là Admin mới được vào
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tài Liệu (Admin) - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .card-custom { background: #ffffff; border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); padding: 40px; }
        .form-label { font-size: 0.85rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 14px 18px; font-weight: 500; background-color: #f8fafc; }
        .form-control:focus, .form-select:focus { border-color: #4318FF; box-shadow: 0 0 0 4px rgba(67, 24, 255, 0.1); background-color: #fff; }
        .link-zone { background-color: #f0f4ff; border: 2px dashed #a5b4fc; border-radius: 16px; padding: 25px; transition: 0.3s; }
        .link-zone:hover { border-color: #4318FF; background-color: #eef2ff; }
        .btn-save { background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%); color: white; padding: 14px 28px; border-radius: 12px; font-weight: 700; border: none; transition: 0.3s; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(67, 24, 255, 0.3); color: white; }
    </style>
</head>
<body>

<?php 
// Nhúng thanh Navbar
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/frontend/includes/navbar.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/frontend/includes/navbar.php';
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="mb-4">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 fw-medium">
                        <li class="breadcrumb-item"><a href="dashboard.php" class="text-decoration-none text-muted">Trang quản trị</a></li>
                        <li class="breadcrumb-item"><a href="documents.php" class="text-decoration-none text-muted">Quản lý tài liệu</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Thêm tài liệu mới</li>
                    </ol>
                </nav>
            </div>

            <div class="card card-custom">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-800 text-dark mb-1"><i class="bi bi-file-earmark-plus-fill text-primary me-2"></i>Thêm Tài Liệu Mới</h4>
                        <p class="text-muted small mb-0">Chia sẻ tài liệu thông qua đường dẫn (Link Drive/PDF) tốc độ cao.</p>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold"><i class="bi bi-shield-lock-fill me-1"></i> Mode: Admin</span>
                </div>

                <form action="/LAPTRINHWEB/backend/documents/upload_document.php" method="POST">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tên giáo trình / Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="VD: Tiếng Anh Cơ Bản" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tác giả / Nhà xuất bản <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" placeholder="VD: Nguyễn Văn Tài" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Chuyên mục học tập <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
    <option value="" disabled selected>-- Chọn chuyên mục --</option>
    <option value="1">Lập trình Web / Mobile</option>
    <option value="2">Cơ sở dữ liệu</option>
    <option value="3">Thiết kế / Kiến trúc</option>
    <option value="4">Ngoại ngữ & Tiếng Anh</option>
    </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link Ảnh Bìa (Thumbnail)</label>
                            <input type="url" name="thumbnail" class="form-control" placeholder="Dán link ảnh bìa...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Mô tả tóm tắt tài liệu</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Nhập tóm tắt sơ lược..."></textarea>
                    </div>

                    <div class="mb-5 link-zone">
                        <label class="form-label text-primary fs-6"><i class="bi bi-link-45deg fs-4 me-1"></i>Đường Dẫn Tài Liệu (Bắt buộc) <span class="text-danger">*</span></label>
                        <input type="url" name="file_path" class="form-control form-control-lg border-primary" placeholder="Dán link Google Drive hoặc link PDF của bạn vào đây..." required>
                        <div class="form-text text-muted mt-2 fw-medium"><i class="bi bi-info-circle me-1"></i>Mẹo: Nếu dùng Google Drive, hãy nhớ bật chế độ <strong>"Bất kỳ ai có đường liên kết"</strong> nhé!</div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="documents.php" class="btn btn-light fw-bold py-2 px-4 border text-secondary rounded-3">Hủy bỏ</a>
                        <button type="submit" class="btn btn-save"><i class="bi bi-cloud-check-fill me-2"></i>Lưu & Phát Hành</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>