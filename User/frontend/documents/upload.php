<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Kiểm tra bảo mật: Nếu chưa đăng nhập thành viên, đá ra trang login
if (!isset($_SESSION['user_id'])) {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';

// Lấy danh sách chuyên mục từ database để đổ vào ô Select option
$categories = [];
try {
    $categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải Lên Tài Liệu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .upload-container { max-width: 600px; margin: 60px auto; }
        .card-custom { background: #ffffff; border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .btn-submit { background-color: #0d6efd; color: white; font-weight: 600; padding: 12px; border-radius: 8px; border: none; width: 100%; transition: 0.2s; }
        .btn-submit:hover { background-color: #0b5ed7; }
    </style>
</head>
<body>

<div class="container upload-container">
    <div class="card card-custom p-4 p-md-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>Tải Lên Tài Liệu</h3>
            <p class="text-muted small">Chọn tệp tin bài tập, giáo trình từ máy tính của bạn để gửi cho Admin duyệt.</p>
        </div>

        <form action="/AIStudyHub/User/backend/documents/upload_document.php" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Tên giáo trình / tài liệu</label>
                <input type="text" name="title" class="form-control py-2" placeholder="Nhập tên tài liệu..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Tác giả</label>
                <input type="text" name="author" class="form-control py-2" placeholder="Tên tác giả hoặc NXB..." required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">Chuyên mục môn học</label>
                <select name="category_id" class="form-select py-2" required>
                    <option value="">-- Chọn chuyên mục học tập --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-primary"><i class="bi bi-laptop me-1"></i>Chọn file từ máy tính của bạn</label>
                <input type="file" name="document_file" class="form-control py-2" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>
                <div class="form-text text-muted">Hỗ trợ các file văn phòng: PDF, Word, PowerPoint, TXT.</div>
            </div>

            <button type="submit" class="btn btn-submit"><i class="bi bi-upload me-2"></i>Gửi Duyệt Tài Liệu</button>
            
            <div class="text-center mt-3">
                <a href="/AIStudyHub/User/frontend/documents/document.php" class="text-decoration-none small text-secondary"><i class="bi bi-arrow-left me-1"></i>Quay lại trang chính</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>