<?php
// connectdb.php
$host = 'localhost';
$port = '3307'; // Đã đồng bộ theo cổng mới trong file my.ini của bạn
$dbname = 'aistudyhub';
$username = 'root';
$password = ''; // Để trống theo quy định của nhóm [cite: 60]

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
}
?>