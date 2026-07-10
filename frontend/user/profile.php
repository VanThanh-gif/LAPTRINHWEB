<?php
// Bật lỗi để debug
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bắt buộc phải đăng nhập mới được vào xem hồ sơ
if (!isset($_SESSION['user_id'])) {
    header("Location: ../guest/login.php");
    exit();
}

require_once __DIR__ . '/../../config/connectdb.php';

// Khởi tạo thông tin mặc định từ Session
$user_name = $_SESSION['username'] ?? 'Thành viên AI Study Hub';
$user_email = $_SESSION['email'] ?? 'Chưa cập nhật email';
$user_role = $_SESSION['role'] ?? 'user';
$join_date = 'Mới tham gia';

// (Tùy chọn) Lấy thêm thông tin chi tiết từ Database nếu cần
try {
    $stmt = $conn->prepare("SELECT email, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        $user_email = $user_data['email'];
        if (!empty($user_data['created_at'])) {
            $join_date = date('d/m/Y', strtotime($user_data['created_at']));
        }
    }
} catch (PDOException $e) {
    // Bỏ qua nếu lỗi database, vẫn hiển thị UI bằng data của Session
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Cá Nhân - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }
        
        .profile-header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            height: 200px;
            position: relative;
        }
        
        .profile-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            margin-top: -100px; /* Kéo thẻ card trồi lên trên nền xanh */
            padding: 40px;
            position: relative;
            z-index: 10;
        }

        .avatar-container {
            text-align: center;
            margin-top: -80px; /* Kéo Avatar lên góc trên cùng của card */
            margin-bottom: 20px;
        }

        .avatar-img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            border: 6px solid #ffffff;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            object-fit: cover;
            background-color: white;
        }

        .info-label {
            font-size: 0.85rem;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.1rem;
            color: #0f172a;
            font-weight: 600;
            padding: 12px 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
        }

        .btn-edit {
            background-color: #3b82f6;
            color: white;
            border-radius: 12px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-edit:hover { background-color: #2563eb; transform: translateY(-2px); }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="profile-header"></div>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="profile-card">
                
                <div class="avatar-container">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=0D6EFD&color=fff&size=256" alt="Avatar" class="avatar-img">
                </div>
                
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-dark mb-1"><?= htmlspecialchars($user_name) ?></h2>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-person-badge"></i> 
                        Vai trò: <?= ($user_role === 'admin') ? 'Quản trị viên (Admin)' : 'Sinh viên (User)' ?>
                    </span>
                </div>

                <h5 class="fw-bold mb-4 border-bottom pb-2">Thông tin tài khoản</h5>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="info-label">Họ và tên</div>
                        <div class="info-value"><i class="bi bi-person me-2 text-primary"></i> <?= htmlspecialchars($user_name) ?></div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-label">Địa chỉ Email</div>
                        <div class="info-value"><i class="bi bi-envelope me-2 text-primary"></i> <?= htmlspecialchars($user_email) ?></div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-label">Ngày tham gia hệ thống</div>
                        <div class="info-value"><i class="bi bi-calendar-check me-2 text-primary"></i> <?= $join_date ?></div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-label">Trạng thái</div>
                        <div class="info-value text-success"><i class="bi bi-check-circle-fill me-2"></i> Đang hoạt động</div>
                    </div>
                </div>

                <div class="text-center mt-5">
                    <a href="edit_profile.php" class="btn btn-edit text-decoration-none">
                        <i class="bi bi-pencil-square me-1"></i> Cập nhật thông tin
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>