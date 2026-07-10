<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Lỗi: Bạn không có quyền truy cập khu vực Quản trị!'); window.location.href='../guest/login.php';</script>";
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

// Lấy thống kê
$total_docs = 0; $total_users = 0;
try {
    $total_docs = $conn->query("SELECT COUNT(*) FROM documents")->fetchColumn();
    $total_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    // Lấy 5 tài liệu mới nhất
    $stmt = $conn->query("SELECT * FROM documents ORDER BY created_at DESC LIMIT 5");
    $recent_docs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $recent_docs = [];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f4f7fe; /* Màu nền dịu hơn, tone xanh nhạt */
            color: #2b3674;
        }
        
        /* BANNER CHÀO MỪNG XỊN XÒ */
        .welcome-section {
            background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%);
            color: white; 
            border-radius: 24px; 
            padding: 40px; 
            margin-bottom: 40px;
            box-shadow: 0 15px 35px -10px rgba(67, 24, 255, 0.4); 
            position: relative; 
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .welcome-section::after { 
            content: "\F135"; font-family: "bootstrap-icons"; 
            position: absolute; right: 5%; top: -10%; 
            font-size: 12rem; opacity: 0.08; transform: rotate(15deg); 
        }

        /* THẺ THỐNG KÊ (STAT CARDS) */
        .stat-card { 
            background: white; 
            border-radius: 20px; 
            padding: 24px; 
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); 
            border: none; 
            transition: all 0.3s ease; 
            height: 100%; 
        }
        .stat-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1); 
        }
        .stat-icon { 
            width: 56px; height: 56px; 
            border-radius: 16px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.8rem; 
        }
        
        /* CÁC KHỐI CONTENT */
        .content-card { 
            background: white; 
            border-radius: 24px; 
            padding: 30px; 
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05); 
            border: none; 
            margin-bottom: 30px; 
        }
        
        /* BẢNG DỮ LIỆU ĐẸP MẮT */
        .table-custom { vertical-align: middle; }
        .table-custom thead th { 
            background-color: transparent; 
            color: #a3aed1; 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            padding: 15px 10px; 
            border-bottom: 2px solid #f4f7fe; 
        }
        .table-custom tbody td { 
            padding: 18px 10px; 
            color: #2b3674; 
            border-bottom: 1px solid #f4f7fe; 
            font-weight: 500;
        }
        .table-custom tbody tr:hover { background-color: #f8faff; }
        
        .badge-soft { padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 0.75rem; letter-spacing: 0.5px;}
        .bg-soft-primary { background-color: #e2e8f0; color: #4318FF; }
        .bg-soft-warning { background-color: #fff3cd; color: #d97706; }
        .bg-soft-success { background-color: #d1e7dd; color: #0f5132; }
        
        /* NÚT THAO TÁC */
        .btn-primary-custom {
            background-color: #4318FF;
            border: none;
            color: white;
            border-radius: 12px;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background-color: #3311db;
            box-shadow: 0 8px 20px -6px rgba(67, 24, 255, 0.5);
            color: white;
            transform: translateY(-2px);
        }

        .btn-action { 
            width: 36px; height: 36px; 
            display: inline-flex; align-items: center; justify-content: center; 
            border-radius: 10px; border: none; transition: 0.2s; text-decoration: none; 
        }
        .btn-edit { background-color: #f4f7fe; color: #4318FF; }
        .btn-edit:hover { background-color: #4318FF; color: white; }
        .btn-delete { background-color: #fee2e2; color: #ef4444; }
        .btn-delete:hover { background-color: #ef4444; color: white; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container mt-4 mb-5">
    
    <div class="welcome-section">
        <h2 class="fw-bolder mb-2">Chào mừng trở lại, Super Admin! ✨</h2>
        <p class="mb-0 text-white-50 fs-6 fw-medium">Hệ thống AI Study Hub đang hoạt động hoàn hảo. Cùng xem báo cáo hôm nay nhé.</p>
    </div>

    <!-- 4 Thống kê -->
    <div class="row g-4 mb-5">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3"><i class="bi bi-folder2-open"></i></div>
                <div><p class="text-secondary mb-1 fw-bold small text-uppercase">Tổng Tài Liệu</p><h3 class="fw-bolder mb-0"><?= $total_docs ?></h3></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-success bg-opacity-10 text-success me-3"><i class="bi bi-people"></i></div>
                <div><p class="text-secondary mb-1 fw-bold small text-uppercase">Thành Viên</p><h3 class="fw-bolder mb-0"><?= $total_users ?></h3></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3"><i class="bi bi-lightning-charge"></i></div>
                <div><p class="text-secondary mb-1 fw-bold small text-uppercase">Tương tác AI</p><h3 class="fw-bolder mb-0">1,204</h3></div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card d-flex align-items-center">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3"><i class="bi bi-cloud-arrow-down"></i></div>
                <div><p class="text-secondary mb-1 fw-bold small text-uppercase">Lượt Tải Về</p><h3 class="fw-bolder mb-0">856</h3></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Cột trái: Biểu đồ & Bảng dữ liệu -->
        <div class="col-lg-8">
            <div class="content-card">
                <h5 class="fw-bold mb-4">Lưu lượng truy cập hệ thống</h5>
                <canvas id="trafficChart" height="100"></canvas>
            </div>

            <!-- BẢNG QUẢN LÝ TÀI LIỆU MỚI NHẤT -->
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold m-0">Tài liệu mới đăng tải</h5>
                    <a href="documents.php" class="text-decoration-none fw-bold" style="color: #4318FF;">Xem tất cả <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th width="10%">ID</th>
                                <th width="35%">Tên tài liệu</th>
                                <th width="20%">Thể loại</th>
                                <th width="15%">Trạng thái</th>
                                <th width="20%" class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_docs as $doc): ?>
                            <tr>
                                <td class="text-secondary">#<?= $doc['document_id'] ?></td>
                                <td class="fw-bold"><?= htmlspecialchars($doc['title']) ?></td>
                                <td><span class="badge-soft bg-soft-primary"><?= htmlspecialchars($doc['file_type'] ?? 'Khác') ?></span></td>
                                <td>
                                    <?php if(($doc['status'] ?? 'pending') == 'pending'): ?>
                                        <span class="badge-soft bg-soft-warning">Chờ duyệt</span>
                                    <?php else: ?>
                                        <span class="badge-soft bg-soft-success">Hoạt động</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="edit_document.php?id=<?= $doc['document_id'] ?>" class="btn-action btn-edit me-1" title="Chỉnh sửa"><i class="bi bi-pencil-fill"></i></a>
                                    <a href="delete_document.php?id=<?= $doc['document_id'] ?>" class="btn-action btn-delete" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa tài liệu này vĩnh viễn?');"><i class="bi bi-trash3-fill"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($recent_docs)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-5">Chưa có tài liệu nào trong hệ thống.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Cột phải: Truy cập nhanh -->
        <div class="col-lg-4">
            <div class="content-card h-100">
                <h5 class="fw-bold mb-4">Bảng điều khiển nhanh</h5>
                
                <div class="d-grid gap-3 mb-5">
                    <a href="add_document.php" class="btn btn-primary-custom fw-bold py-3">
                        <i class="bi bi-plus-circle me-2"></i>Thêm tài liệu mới
                    </a>
                    <a href="users.php" class="btn btn-light fw-bold py-3 text-primary border" style="border-radius: 12px;">
                        <i class="bi bi-person-gear me-2"></i>Quản lý người dùng
                    </a>
                </div>

                <h6 class="fw-bold mb-4">Hoạt động gần đây</h6>
                <div class="d-flex align-items-start mb-4">
                    <div class="bg-success rounded-circle p-2 me-3 text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-person-plus-fill small"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold fs-6">Người dùng mới</p>
                        <p class="text-secondary small mb-0">Nguyễn Văn A vừa đăng ký tài khoản</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="bg-primary rounded-circle p-2 me-3 text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                        <i class="bi bi-file-earmark-arrow-up-fill small"></i>
                    </div>
                    <div>
                        <p class="mb-0 fw-bold fs-6">Cập nhật tài liệu</p>
                        <p class="text-secondary small mb-0">Admin đã tải lên tài liệu mới</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Config Chart.js làm cho mượt mà hơn
    const ctx = document.getElementById('trafficChart').getContext('2d');
    
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(67, 24, 255, 0.2)');
    gradient.addColorStop(1, 'rgba(67, 24, 255, 0)');

    new Chart(ctx, { 
        type: 'line', 
        data: { 
            labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'], 
            datasets: [{ 
                label: 'Lượt truy cập', 
                data: [120, 190, 150, 250, 220, 310, 280], 
                borderColor: '#4318FF', 
                backgroundColor: gradient, 
                borderWidth: 4, 
                tension: 0.4, 
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#4318FF',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }] 
        }, 
        options: { 
            responsive: true, 
            plugins: { legend: { display: false } }, 
            scales: { 
                y: { beginAtZero: true, grid: { borderDash: [5, 5], color: '#e2e8f0' }, border: {display: false} },
                x: { grid: { display: false }, border: {display: false} }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
        } 
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>