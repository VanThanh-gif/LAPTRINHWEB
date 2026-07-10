<?php
// Bật hiển thị lỗi
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Central auth + DB
require_once __DIR__ . '/../../includes/auth_check.php';
require_login();
require_once __DIR__ . '/../../config/connectdb.php';

$user_id = $_SESSION['user_id'];
$message = '';
$msg_type = '';

// Lấy thông tin hiện tại từ DB để điền sẵn vào Form
try {
    $stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$current_user) {
        $current_user = ['username' => $_SESSION['username'], 'email' => ''];
    }
} catch (PDOException $e) {
    $current_user = ['username' => $_SESSION['username'], 'email' => ''];
}

// XỬ LÝ KHI NGƯỜI DÙNG BẤM NÚT "LƯU THAY ĐỔI"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = trim($_POST['username']);
    $new_email = trim($_POST['email']);
    $new_password = $_POST['new_password'];

    if (empty($new_name) || empty($new_email)) {
        $message = "Tên và Email không được để trống!";
        $msg_type = "danger";
    } else {
        // Validate email uniqueness first
        try {
            $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1");
            $check->execute([$new_email, $user_id]);
            $conflict = $check->fetch(PDO::FETCH_ASSOC);
            if ($conflict) {
                $message = "Email này đã được sử dụng bởi tài khoản khác.";
                $msg_type = "danger";
            } else {
                // No conflict — proceed with updates
                // Handle avatar upload (optional)
                $avatar_path = null;
                if (isset($_FILES['avatar']) && isset($_FILES['avatar']['tmp_name']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
                    finfo_close($finfo);

                    if (!isset($allowed[$mime])) {
                        $message = "File avatar không hợp lệ (chỉ JPG/PNG/GIF).";
                        $msg_type = "danger";
                    } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
                        $message = "Kích thước avatar tối đa 2MB.";
                        $msg_type = "danger";
                    } else {
                        $ext = $allowed[$mime];
                        $uploadDir = __DIR__ . '/../../Uploads/avatars';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                        $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
                        $dest = $uploadDir . '/' . $filename;
                        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest)) {
                            $avatar_path = '/LAPTRINHWEB/Uploads/avatars/' . $filename;
                        } else {
                            $message = "Không thể lưu file avatar, thử lại.";
                            $msg_type = "danger";
                        }
                    }
                }

                // If no avatar/file errors, do DB updates
                if (empty($message)) {
                    if (!empty($new_password)) {
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE user_id = ?");
                        $update_stmt->execute([$new_name, $new_email, $hashed_password, $user_id]);
                    } else {
                        $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
                        $update_stmt->execute([$new_name, $new_email, $user_id]);
                    }

                    // Update avatar column if file uploaded
                    if ($avatar_path) {
                        try {
                            $stmtAvatar = $conn->prepare("UPDATE users SET avatar = ? WHERE user_id = ?");
                            $stmtAvatar->execute([$avatar_path, $user_id]);
                            $_SESSION['avatar'] = $avatar_path;
                        } catch (PDOException $e) {
                            // ignore if avatar column missing
                        }
                    }

                    // Update session and current_user
                    $_SESSION['username'] = $new_name;
                    $_SESSION['email'] = $new_email;
                    $message = "Cập nhật hồ sơ thành công!";
                    $msg_type = "success";
                    $current_user['username'] = $new_name;
                    $current_user['email'] = $new_email;

                    // Redirect to profile page so navbar/avatar refreshes immediately
                    header('Location: profile.php?updated=1');
                    exit;
                }
            }
        } catch (PDOException $e) {
            $message = "Lỗi hệ thống khi cập nhật, thử lại sau.";
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
    <title>Chỉnh Sửa Hồ Sơ - AI Study Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f3f4f6; }
        
        .edit-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
            padding: 40px;
            margin-top: 40px;
            border: 1px solid #e2e8f0;
        }

        .form-label { font-weight: 600; color: #334155; margin-bottom: 8px; }
        .form-control {
            border-radius: 12px;
            padding: 14px 20px;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            transition: all 0.2s;
        }
        .form-control:focus {
            border-color: #3b82f6;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }
        
        .btn-save {
            background-color: #2563eb;
            color: white;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 700;
            transition: all 0.3s;
        }
        .btn-save:hover { background-color: #1d4ed8; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(37,99,235,0.2); color: white;}
        
        .btn-cancel {
            background-color: #f1f5f9;
            color: #64748b;
            border-radius: 12px;
            padding: 14px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .avatar-preview {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            overflow: hidden;
            background: #f3f4f6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #d1d5db;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }
        .btn-cancel:hover { background-color: #e2e8f0; color: #0f172a; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            
            <div class="d-flex align-items-center mt-5 mb-2">
                <a href="profile.php" class="text-decoration-none text-muted me-3 fs-5"><i class="bi bi-arrow-left-circle-fill"></i></a>
                <h3 class="fw-bold text-dark m-0">Cập nhật hồ sơ</h3>
            </div>

            <div class="edit-card">
                
                <?php if ($message): ?>
                    <div class="alert alert-<?= $msg_type ?> alert-dismissible fade show rounded-4" role="alert">
                        <i class="bi bi-info-circle-fill me-2"></i> <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-4">
                        <label class="form-label">Họ và tên hiển thị</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-4 px-3"><i class="bi bi-person text-muted"></i></span>
                            <input type="text" name="username" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($current_user['username']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Địa chỉ Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-4 px-3"><i class="bi bi-envelope text-muted"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($current_user['email']) ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Ảnh đại diện (Avatar)</label>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="avatar-preview" id="avatarPreview">
                                <img src="<?= !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username'] ?? $current_user['username']) . '&background=0D6EFD&color=fff&size=256' ?>" alt="Avatar Preview" />
                            </div>
                            <div class="flex-grow-1">
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="form-control">
                                <small class="text-muted">Kích thước tối đa 2MB. JPG/PNG/GIF.</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <div class="mb-5">
                        <label class="form-label d-flex justify-content-between">
                            <span>Mật khẩu mới</span>
                            <small class="text-muted fw-normal">(Bỏ trống nếu không muốn đổi)</small>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 rounded-start-4 px-3"><i class="bi bi-shield-lock text-muted"></i></span>
                            <input type="password" name="new_password" class="form-control border-start-0 ps-0" placeholder="Nhập mật khẩu mới...">
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-save flex-grow-1"><i class="bi bi-check2-circle me-2"></i> Lưu Thay Đổi</button>
                        <a href="profile.php" class="btn btn-cancel">Hủy bỏ</a>
                    </div>

                </form>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var avatarInput = document.getElementById('avatarInput');
        var avatarPreview = document.querySelector('#avatarPreview img');

        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function () {
                var file = this.files[0];
                if (!file) return;
                var reader = new FileReader();
                reader.onload = function (e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
</body>
</html>