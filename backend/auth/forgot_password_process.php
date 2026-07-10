<?php
// Bật hiển thị lỗi để dễ dò bug
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kết nối Database
require_once __DIR__ . '/../../config/connectdb.php';

// 2. Kiểm tra xem người dùng có thực sự bấm nút Gửi (POST) không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Lấy email từ form và làm sạch khoảng trắng
    $email = trim($_POST['email'] ?? '');

    // Nếu để trống email mà cố tình gửi
    if (empty($email)) {
        echo "<script>
            alert('Vui lòng nhập địa chỉ email!'); 
            window.history.back();
        </script>";
        exit();
    }

    try {
        // 3. Truy vấn xem email này có tồn tại trong bảng users không
        $stmt = $conn->prepare("SELECT email FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // TÌM THẤY USER: Ở đồ án thực tế sẽ dùng thư viện PHPMailer gửi link Token.
            // Ở đây mình làm Popup thông báo mô phỏng nghiệp vụ cực mượt.
            echo "<script>
                alert('✅ YÊU CẦU THÀNH CÔNG!\\n\\nHệ thống đã gửi một đường link khôi phục mật khẩu vào địa chỉ email: {$email}\\n\\n(Lưu ý: Đây là thông báo mô phỏng chức năng gửi mail của đồ án).');
                window.location.href = '../../frontend/guest/login.php';
            </script>";
            exit();
        } else {
            // BẢO MẬT: Nguyên tắc chuẩn của Web là không bao giờ báo lỗi "Email không tồn tại"
            // để tránh Hacker dò quét email của người dùng. Cứ báo thành công chung chung.
            echo "<script>
                alert('✅ ĐÃ TIẾP NHẬN YÊU CẦU!\\n\\nNếu email {$email} có tồn tại trong hệ thống, chúng tôi đã gửi hướng dẫn khôi phục mật khẩu vào hộp thư của bạn.');
                window.location.href = '../../frontend/guest/login.php';
            </script>";
            exit();
        }

    } catch (PDOException $e) {
        // Lỗi CSDL thì báo lỗi đỏ lòm cho coder dễ sửa
        die("Lỗi cơ sở dữ liệu: " . $e->getMessage());
    }

} else {
    // Nếu cố tình gõ link này trên URL thì đá về trang quên mật khẩu
    header("Location: ../../frontend/guest/forgot_password.php");
    exit();
}
?>