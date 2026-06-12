<?php
// 1. Bật hiển thị lỗi tối đa để triệt tiêu hoàn toàn màn hình trắng nếu có trục trặc database
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. Nhúng file statistics bằng đường dẫn tương đối chính xác (lùi 3 cấp để ra htdocs/AIStudyHub rồi vào backend)
require_once '../../../Admin/backend/dashboard/statistics.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ Thống Quản Trị - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --main-bg: #f8fafc;
        }
        body { background-color: var(--main-bg); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: var(--sidebar-bg); color: white; box-shadow: 2px 0 5px rgba(0,0,0,0.05); }
        .sidebar a { color: #94a3b8; text-decoration: none; display: block; padding: 14px 24px; font-weight: 500; transition: all 0.2s; }
        .sidebar a:hover, .sidebar a.active { background-color: var(--sidebar-hover); color: #f8fafc; border-left: 4px solid #38bdf8; }
        .card-custom { border: none; border-radius: 12px; transition: transform 0.2s, box-shadow 0.2s; }
        .card-custom:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 px-0 sidebar d-none d-md-block">
            <div class="p-4 text-center border-bottom border-secondary border-opacity-25">
                <h5 class="text-info mb-0 fw-bold"><i class="bi bi-cpu-fill me-2"></i>AI Study Hub</h5>
                <small class="text-uppercase tracking-wider text-muted style" style="font-size: 0.75rem;">Workspace Admin</small>
            </div>
            <div class="py-3">
                <a href="#" class="active"><i class="bi bi-grid-1x2-fill me-3"></i>Tổng quan</a>
                <a href="../users/users.php"><i class="bi bi-people-fill me-3"></i>Quản lý Thành viên</a>
                <a href="../documents/documents.php"><i class="bi bi-file-earmark-text-fill me-3"></i>Duyệt Tài liệu</a>
                <a href="../chatbot/chats.php"><i class="bi bi-chat-square-quote-fill me-3"></i>Lịch sử Chatbot</a>
                <hr class="mx-3 text-secondary opacity-25">
                <a href="../../../Guest/frontend/auth/login.php" class="text-danger"><i class="bi bi-box-arrow-left me-3"></i>Đăng xuất</a>
            </div>
        </div>

        <div class="col-md-10 p-4">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <div>
                    <h3 class="fw-bold text-slate-800 mb-0">Bảng điều khiển hệ thống</h3>
                    <p class="text-muted small mb-0">Cập nhật dữ liệu thời gian thực từ hệ thống CSDL.</p>
                </div>
                <span class="badge bg-white text-dark shadow-sm p-2 border"><i class="bi bi-person-circle text-info me-2"></i>Quyền: Administrator</span>
            </div>

            <div class="row g-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-custom bg-white p-3 shadow-sm border-start border-primary border-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Tổng Sinh Viên</p>
                                <h3 class="fw-bold text-dark mb-0"><?php echo isset($total_users) ? $total_users : 0; ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary"><i class="bi bi-people fs-3"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-custom bg-white p-3 shadow-sm border-start border-warning border-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">File Chờ Kiểm Duyệt</p>
                                <h3 class="fw-bold text-dark mb-0"><?php echo isset($pending_docs) ? $pending_docs : 0; ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning"><i class="bi bi-hourglass-split fs-3"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-custom bg-white p-3 shadow-sm border-start border-success border-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Tài Liệu Đã Duyệt</p>
                                <h3 class="fw-bold text-dark mb-0"><?php echo isset($approved_docs) ? $approved_docs : 0; ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success"><i class="bi bi-file-check fs-3"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card card-custom bg-white p-3 shadow-sm border-start border-info border-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-muted small text-uppercase fw-bold mb-1">Tổng Câu Hỏi AI</p>
                                <h3 class="fw-bold text-dark mb-0"><?php echo isset($total_chats) ? $total_chats : 0; ?></h3>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded-3 text-info"><i class="bi bi-chat-dots fs-3"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 p-4 bg-white rounded-3 shadow-sm border">
                <h5 class="fw-bold text-success"><i class="bi bi-patch-check-fill me-2"></i>Kết nối thành công!</h5>
                <p class="text-muted mb-0">Giao diện quản trị đã liên kết thành công tới lõi cơ sở dữ liệu. Bạn có thể sử dụng Menu bên trái để điều hướng sang các tính năng quản lý chi tiết.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>