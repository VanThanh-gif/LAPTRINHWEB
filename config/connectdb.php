<?php
// Đường dẫn thực tế trên XAMPP: C:\xampp\htdocs\LAPTRINHWEB\config\connectdb.php
$host = "localhost:3307"; // ✅ SỬA TẠI ĐÂY: Đổi từ "localhost" thành "localhost:3307" để ăn khớp với cổng MySQL của Tài
$dbname = "aistudyhub";
$username_db = "root";
$password_db = ""; // Nếu máy Tài có đặt pass cho root thì điền vào đây, không thì để trống

try {
    // Khởi tạo đối tượng kết nối PDO chuẩn hóa tiếng Việt utf8
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
    // Kích hoạt chế độ báo lỗi ngoại lệ để bảo mật và gỡ lỗi nhanh
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Hệ thống bảo trì - Không thể liên kết cơ sở dữ liệu local: " . $e->getMessage());
}
?>