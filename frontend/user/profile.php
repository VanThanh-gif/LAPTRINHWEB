<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_id'])) {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

// Lấy thông tin hiện tại từ DB để fill vào form
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?"); 
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Chuẩn bị ảnh dự phòng
$fallback_avatar = "https://ui-avatars.com/api/?name=" . urlencode($user['username'] ?? 'User') . "&background=random";
$avatar_url = !empty($user['avatar']) ? $user['avatar'] : $fallback_avatar;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif; color: #1e293b; }
        .card-profile { background: #ffffff; border: none; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); padding: 40px; position: relative; overflow: hidden; }
        .card-profile::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 120px; background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%); z-index: 1; }
        .avatar-wrapper { position: relative; width: 130px; height: 130px; margin: 0 auto 20px; z-index: 2; margin-top: 40px; }
        .avatar-preview { width: 130px; height: 130px; border-radius: 50%; object-fit: cover; border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); background-color: #fff; }
        .upload-btn { position: absolute; bottom: 5px; right: 5px; background: #4318FF; color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: 3px solid #fff; transition: 0.3s; }
        .upload-btn:hover { background: #39B8FF; transform: scale(1.1); }
        .form-label { font-size: 0.8rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-control { border-radius: 12px; padding: 12px 18px; font-weight: 500; background-color: #f8fafc; border: 1.5px solid #e2e8f0; }
        .form-control:focus { border-color: #4318FF; box-shadow: 0 0 0 4px rgba(67, 24, 255, 0.1); background-color: #fff; }
        .btn-save { background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%); color: white; padding: 14px 0; border-radius: 12px; font-weight: 700; border: none; transition: 0.3s; width: 100%; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(67, 24, 255, 0.3); }
    </style>
</head>
<body>

<?php 
// ĐÃ FIX LỖI ĐƯỜNG DẪN THƯ MỤC INCLUDES 
include_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/includes/navbar.php'; 
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
           <form action="/LAPTRINHWEB/backend/auth/update_profile.php" method="POST" enctype="multipart/form-data" class="card card-profile">
                
                <a href="document.php" class="text-white text-decoration-none fw-bold" style="position: absolute; top: 25px; left: 30px; z-index: 2;"><i class="bi bi-arrow-left me-2"></i>Trở về</a>

                <div class="avatar-wrapper text-center">
                    <img id="avatar-img" src="<?= $avatar_url ?>" onerror="this.onerror=null; this.src='<?= $fallback_avatar ?>';" alt="Avatar" class="avatar-preview">
                    <label for="avatar-input" class="upload-btn" title="Thay đổi ảnh đại diện">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                    <input type="file" id="avatar-input" name="avatar" class="d-none" accept="image/png, image/jpeg, image/jpg">
                </div>

                <div class="text-center mb-5">
                    <h4 class="fw-800 text-dark mb-1"><?= htmlspecialchars($user['username'] ?? 'Tên hiển thị') ?></h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold"><i class="bi bi-person-check-fill me-1"></i> Sinh viên</span>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">THÔNG TIN TÀI KHOẢN</h6>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-person me-1"></i> Họ và tên</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-envelope me-1"></i> Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-telephone me-1"></i> Số điện thoại</label>
                        <input type="text" name="phone" class="form-control" placeholder="09xx xxx xxx" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-mortarboard me-1"></i> Chuyên ngành</label>
                        <input type="text" name="major" class="form-control" placeholder="VD: Công nghệ thông tin..." value="<?= htmlspecialchars($user['major'] ?? '') ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-save mt-2"><i class="bi bi-cloud-arrow-up-fill me-2"></i>Lưu Thay Đổi</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('avatar-input').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-img').src = e.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
</body>
</html>