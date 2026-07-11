<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /LAPTRINHWEB/frontend/guest/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $major = trim($_POST['major'] ?? '');

    if (empty($username) || empty($email)) {
        echo "<script>alert('Họ tên và Email không được để trống!'); window.history.back();</script>";
        exit();
    }

    try {
        // Cập nhật thông tin (bỏ phone và major nếu DB của sếp chưa có 2 cột này nhé)
        $sql = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username, $email, $user_id]);

        $_SESSION['username'] = $username;

        // XỬ LÝ UPLOAD AVATAR
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($file_ext, $allowed_exts)) {
                $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/Uploads/'; // Đổi tạm ra thư mục Uploads ngoài cùng của sếp
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                $new_filename = 'avatar_' . $user_id . '_' . time() . '.' . $file_ext;
                $destination = $upload_dir . $new_filename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $avatar_path = '/LAPTRINHWEB/Uploads/' . $new_filename;
                    
                    $sql_img = "UPDATE users SET avatar = ? WHERE user_id = ?";
                    $stmt_img = $conn->prepare($sql_img);
                    $stmt_img->execute([$avatar_path, $user_id]);

                    $_SESSION['avatar'] = $avatar_path;
                }
            }
        }

        echo "<script>alert('Cập nhật thông tin cá nhân thành công!'); window.location.href='/LAPTRINHWEB/frontend/user/profile.php';</script>";
        exit();

    } catch (PDOException $e) {
        echo "<script>alert('Lỗi Database: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>