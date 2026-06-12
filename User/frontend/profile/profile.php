<?php
session_start();
// Chặn truy cập nếu chưa đăng nhập (Auth Base Check)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../Guest/frontend/auth/login.php");
    exit();
}
require_once '../../../connectdb.php';

// Lấy thông tin mới nhất từ database
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân - AIStudyHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include '../../../navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0 fw-bold">HỒ SƠ CÁ NHÂN</h4>
                    </div>
                    <div class="card-body text-center">
                        <img src="../../../uploads/avatars/<?php echo htmlspecialchars($user['avatar']); ?>" class="rounded-circle img-thumbnail mb-3" style="width: 150px; height: 150px; object-fit: cover;" alt="Avatar">
                        
                        <h3 class="fw-bold mb-1"><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                        
                        <ul class="list-group list-group-flush text-start mb-4">
                            <li class="list-group-item"><strong>Vai trò:</strong> <span class="badge bg-secondary"><?php echo strtoupper($user['role']); ?></span></li>
                            <li class="list-group-item"><strong>Ngày tham gia:</strong> <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></li>
                        </ul>

                        <a href="edit_profile.php" class="btn btn-warning px-4">Chỉnh sửa hồ sơ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../../footer.php'; ?>
</body>
</html>