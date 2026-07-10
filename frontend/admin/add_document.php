<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Khu vực dành riêng cho Admin!'); window.location.href='../guest/login.php';</script>";
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

$message = '';
$msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category_value = trim($_POST['category'] ?? '');
    $description_value = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    
    // ĐÂY NÈ: Lấy link tải file từ form (nếu rỗng thì mặc định là #)
    $file_path = trim($_POST['file_path'] ?? '#'); 
    
    $author = $_SESSION['username'] ?? 'Admin';
    $user_id = $_SESSION['user_id'] ?? null; 

    if ($user_id == 9999) {
        $user_id = null;
    }

    if (empty($title) || empty($category_value)) {
        $message = "Vui lòng nhập tên tài liệu và thể loại!";
        $msg_type = "danger";
    } else {
        try {
            $sql = "INSERT INTO documents (user_id, title, file_path, file_type, status, author, description, image) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($sql);
            
            $stmt->execute([
                $user_id,             
                $title,               
                $file_path,           // <--- Đã truyền link thật vào Database
                $category_value,      
                'pending',            
                $author,              
                $description_value,   
                $image_url            
            ]);
            
            $message = "🎉 Đã thêm tài liệu [ {$title} ] lên hệ thống thành công!";
            $msg_type = "success";
            
        } catch (PDOException $e) {
            $message = "Lỗi xử lý hệ thống: " . $e->getMessage();
            $msg_type = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Tài Liệu Mới - Admin AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f1f5f9; }
        .admin-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 20px 0; margin-bottom: 30px; }
        .form-card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; padding: 30px 40px; }
        .form-label { font-weight: 600; color: #334155; margin-bottom: 8px; }
        .form-control, .form-select { border-radius: 10px; padding: 12px 16px; border: 1px solid #cbd5e1; background-color: #f8fafc; }
        .form-control:focus, .form-select:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); background-color: #ffffff; }
        .btn-submit { background-color: #2563eb; color: white; border-radius: 10px; padding: 12px 24px; font-weight: 700; transition: 0.2s; }
        .btn-submit:hover { background-color: #1d4ed8; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="admin-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-dark m-0"><i class="bi bi-file-earmark-plus text-primary me-2"></i>Thêm Tài Liệu Mới</h3>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary fw-bold px-4 py-2 rounded-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Trở về Dashboard
        </a>
    </div>
</div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="form-card">
                
                <?php if ($message): ?>
                    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show rounded-3" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="form-label">Tên tài liệu / Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="VD: Lập trình Web Fullstack với PHP..." required>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Chuyên mục / Thể loại <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="" selected disabled>-- Chọn thể loại --</option>
                                <option value="Lập trình">Lập trình Web / Mobile</option>
                                <option value="Database">Cơ sở dữ liệu</option>
                                <option value="Kiến trúc">Thiết kế / Kiến trúc</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Link Ảnh Bìa (Thumbnail)</label>
                            <input type="text" name="image_url" class="form-control" placeholder="Dán link ảnh tùy chọn...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Link Tải File (Google Drive, Dropbox...) <span class="text-danger">*</span></label>
                        <input type="url" name="file_path" class="form-control" placeholder="Dán link tải tài liệu vào đây (bắt đầu bằng https://...)" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Mô tả ngắn gọn về tài liệu</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Nhập tóm tắt nội dung..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-3 mt-5 border-top pt-4">
                        <a href="documents.php" class="btn btn-light fw-bold px-4 border">Hủy bỏ</a>
                        <button type="submit" class="btn btn-submit"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Đăng Tài Liệu Lên Hệ Thống</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>