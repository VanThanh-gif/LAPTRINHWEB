<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../Guest/frontend/auth/login.php");
    exit();
}
require_once '../../../connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $user_id = $_SESSION['user_id'];

    if (empty($username)) {
        $_SESSION['error'] = "Tên người dùng không được để trống!";
        header("Location: ../../frontend/profile/edit_profile.php");
        exit();
    }

    try {
        // Xử lý Upload Avatar nếu có chọn file mới
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = $_FILES['avatar']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Kiểm tra định dạng đuôi file ảnh hợp lệ
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedExtensions)) {
                // Tạo tên file ngẫu nhiên để tránh trùng lặp trùng tên trên host
                $newFileName = "avatar_" . $user_id . "_" . time() . "." . $fileExtension;
                $uploadFileDir = '../../../uploads/avatars/';
                
                // Tạo thư mục nếu chưa tồn tại
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }

                $dest_path = $uploadFileDir . $newFileName;
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Cập nhật tên avatar mới vào database
                    $stmt = $conn->prepare("UPDATE users SET avatar = ? WHERE user_id = ?");
                    $stmt->execute([$newFileName, $user_id]);
                }
            } else {
                $_SESSION['error'] = "Chỉ chấp nhận file ảnh định dạng JPG, JPEG, PNG, GIF!";
                header("Location: ../../frontend/profile/edit_profile.php");
                exit();
            }
        }

        // Cập nhật các thông tin text khác (như username)
        $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
        $stmt->execute([$username, $user_id]);
        
        // Cập nhật lại session hiển thị tên mới
        $_SESSION['username'] = $username;
        
        $_SESSION['success'] = "Cập nhật hồ sơ thành công!";
        header("Location: ../../frontend/profile/profile.php");
        exit();

    } catch (PDOException $e) {
        $_SESSION['error'] = "Lỗi cập nhật dữ liệu: " . $e->getMessage();
        header("Location: ../../frontend/profile/edit_profile.php");
        exit();
    }
}
?>