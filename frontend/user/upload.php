<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Kiểm tra bảo mật: Thành viên phổ thông phải đăng nhập mới được xài
if (!isset($_SESSION['user_id'])) {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đóng Góp Tài Liệu - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .main-content { padding: 40px 0; }
        .card-custom { background: #ffffff; border: none; border-radius: 24px; box-shadow: 0 15px 35px -10px rgba(67, 24, 255, 0.05); padding: 40px; }
        
        /* Form Styling */
        .form-label { font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control, .form-select { border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 12px 18px; background-color: #f8fafc; font-weight: 500; transition: all 0.2s ease; }
        .form-control:focus, .form-select:focus { border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); background-color: #fff; }
        
        /* Upload Area */
        .upload-zone { border: 2px dashed #cbd5e1; background-color: #f8fafc; border-radius: 16px; padding: 35px; transition: all 0.3s ease; cursor: pointer; }
        .upload-zone:hover { border-color: #10b981; background-color: rgba(16, 185, 129, 0.02); }
        
        /* Buttons */
        .btn-upload { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border: none; color: white; border-radius: 12px; padding: 14px 28px; font-weight: 700; transition: all 0.3s; }
        .btn-upload:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(16, 185, 129, 0.4); color: white; }
        .btn-cancel { border-radius: 12px; padding: 14px 24px; font-weight: 600; color: #64748b; border: 1px solid #e2e8f0; background: #fff; }
        .btn-cancel:hover { background: #f8fafc; color: #334155; }
    </style>
</head>
<body>

<?php 
// Nhúng tuyệt đối thanh Navbar chứa Avatar 
if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/frontend/includes/navbar.php')) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/frontend/includes/navbar.php';
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php';
}
?>

<div class="container main-content">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="mb-4">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 fw-medium">
                        <li class="breadcrumb-item"><a href="document.php" class="text-decoration-none text-muted">Kho tài liệu</a></li>
                        <li class="breadcrumb-item active text-success fw-bold" aria-current="page">Đóng góp tài liệu</li>
                    </ol>
                </nav>
            </div>

            <div class="card card-custom">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-800 mb-1 text-dark"><i class="bi bi-cloud-arrow-up-fill text-success me-2"></i>Đóng Góp Tài Liệu Học Tập</h4>
                        <p class="text-muted small mb-0">Chia sẻ giáo trình, tài liệu hữu ích của bạn để cùng xây dựng cộng đồng học tập vững mạnh.</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 fw-bold rounded-pill">
                        <i class="bi bi-people-fill me-1"></i> Thành viên
                    </span>
                </div>

                <form action="/LAPTRINHWEB/backend/documents/upload_document.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Tên tài liệu / Tiêu đề giáo trình <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="VD: Đề cương ôn thi cuối kỳ Triết học..." required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Tác giả / Nguồn sưu tầm <span class="text-danger">*</span></label>
                            <input type="text" name="author" class="form-control" placeholder="VD: Sưu tầm Đại học Giao thông Vận tải..." required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Chuyên mục môn học <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="" disabled selected>-- Chọn thể loại phù hợp --</option>
                                <option value="Lập trình Web / Mobile">Lập trình Web / Mobile</option>
                                <option value="Cơ sở dữ liệu">Cơ sở dữ liệu</option>
                                <option value="Thiết kế / Kiến trúc">Thiết kế / Kiến trúc</option>
                                <option value="Ngoại ngữ & Tiếng Anh">Ngoại ngữ & Tiếng Anh</option>
                                <option value="Sáng tạo nội dung & Truyền thông">Sáng tạo nội dung & Truyền thông</option>
                                <option value="Thể chất & Dinh dưỡng">Thể chất & Dinh dưỡng</option>
                                <option value="Khác">Khác</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Link Ảnh Minh Họa (Tùy chọn)</label>
                            <input type="url" name="thumbnail" class="form-control" placeholder="Dán link ảnh bìa nếu có...">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Tóm tắt sơ lược nội dung tài liệu</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Viết vài dòng giới thiệu ngắn gọn tài liệu này nói về chủ đề gì để các bạn khác dễ theo dõi nhé..."></textarea>
                    </div>

                    <div class="mb-5">
                        <label class="form-label">Chọn tệp tin từ máy tính của bạn <span class="text-danger">*</span></label>
                        <div class="upload-zone text-center">
                            <i class="bi bi-box-arrow-in-up text-success fs-1 mb-2 d-block"></i>
                            <span class="fw-bold d-block text-secondary mb-3">Nhấp chuột vào đây để chọn file tài liệu muốn đăng</span>
                            <input type="file" name="document_file" class="form-control w-75 mx-auto" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" required>
                            <div class="form-text text-muted mt-2">Chấp nhận định dạng văn bản thông dụng: PDF, Word, PowerPoint, TXT (Tối đa 15MB).</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3 border-top pt-4">
                        <a href="document.php" class="btn btn-cancel">Hủy bỏ</a>
                        <button type="submit" class="btn btn-upload"><i class="bi bi-send-check-fill me-2"></i>Gửi Duyệt Tài Liệu</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>