<?php
// File: frontend/guest/fix_admin.php
require_once __DIR__ . '/../../config/connectdb.php'; 

// Mã hóa mật khẩu "admin123" theo chuẩn bảo mật PHP giống file Register
$hashed_password = password_hash('admin123', PASSWORD_DEFAULT);

try {
    // 1. Kiểm tra xem tài khoản admin@aistudyhub.com đã tồn tại chưa
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = 'admin@aistudyhub.com'");
    $stmt->execute();
    $admin = $stmt->fetch();

    if ($admin) {
        // Nếu đã có tài khoản này rồi -> Cập nhật lại mật khẩu đã mã hóa chuẩn và quyền admin
        $update = $conn->prepare("UPDATE users SET password = ?, role = 'admin', username = 'Quản Trị Viên' WHERE email = 'admin@aistudyhub.com'");
        $update->execute([$hashed_password]);
        echo "<h2 style='color: green;'>Đã CẬP NHẬT mật khẩu mã hóa thành công cho admin@aistudyhub.com!</h2>";
    } else {
        // Nếu chưa có tài khoản này -> Tạo mới tinh luôn
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES ('Quản Trị Viên', 'admin@aistudyhub.com', ?, 'admin')");
        $insert->execute([$hashed_password]);
        echo "<h2 style='color: blue;'>Đã TẠO MỚI thành công tài khoản admin@aistudyhub.com!</h2>";
    }
    
    echo "<p>Bây giờ Tài quay lại trang <a href='login.php'>Đăng nhập</a> thử bằng pass <b>admin123</b> nhé.</p>";

} catch (PDOException $e) {
    echo "<h2 style='color: red;'>Lỗi kết nối DB: " . $e->getMessage() . "</h2>";
    echo "<p>Tài check lại xem file cấu hình database ở đường dẫn config/connectdb.php có đúng không nha.</p>";
}
?>