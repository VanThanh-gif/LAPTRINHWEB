<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/LAPTRINHWEB/config/connectdb.php';

// Kiểm tra bảo mật
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Vui lòng đăng nhập trước!'); window.location.href='/LAPTRINHWEB/frontend/guest/login.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'] ?? 'user';
    
    // Lấy dữ liệu từ Form
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $category_val = trim($_POST['category_id'] ?? ''); 
    $thumbnail = trim($_POST['thumbnail'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $file_path = trim($_POST['file_path'] ?? '');

    if (empty($title) || empty($file_path)) {
        echo "<script>alert('Tên tài liệu và Đường dẫn không được để trống!'); window.history.back();</script>";
        exit();
    }

    $status = ($role === 'admin') ? 'approved' : 'pending'; 

    try {
        // 🚀 BƯỚC 1: Lấy danh sách TẤT CẢ các cột thực tế đang có trong bảng documents của sếp
        $cols = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
        
        $insert_cols = [];
        $insert_vals = [];

        // 🚀 BƯỚC 2: Hàm Tự Động Khớp Cột (CHỈ đưa vào lệnh SQL nếu cột đó thực sự tồn tại)
        $mapColumn = function($possible_names, $value) use (&$insert_cols, &$insert_vals, $cols) {
            foreach ($possible_names as $col_name) {
                if (in_array($col_name, $cols)) {
                    $insert_cols[] = $col_name;
                    $insert_vals[] = $value;
                    return; // Tìm thấy cột đúng thì dừng lại
                }
            }
        };

        // Quét từng trường dữ liệu, rà xem sếp đặt tên cột là gì thì tự động lấy tên đó
        $mapColumn(['title', 'ten_tai_lieu'], $title);
        $mapColumn(['author', 'tac_gia', 'nguoi_dang_tai'], $author);
        $mapColumn(['category_id', 'category', 'the_loai', 'chuyen_muc', 'id_danhmuc'], $category_val);
        $mapColumn(['thumbnail', 'anh_bia', 'image', 'hinh_anh'], $thumbnail);
        $mapColumn(['description', 'mo_ta', 'tom_tat'], $description);
        $mapColumn(['file_path', 'document_url', 'link_file', 'duong_dan', 'file'], $file_path);
        $mapColumn(['uploaded_by', 'user_id', 'nguoi_dang', 'id_user'], $user_id);
        $mapColumn(['status', 'trang_thai'], $status);

        // Khớp cột thời gian (Tự động lấy giờ hiện tại)
        if (in_array('created_at', $cols)) {
            $insert_cols[] = 'created_at';
            $insert_vals[] = date('Y-m-d H:i:s');
        } elseif (in_array('ngay_dang', $cols)) {
            $insert_cols[] = 'ngay_dang';
            $insert_vals[] = date('Y-m-d H:i:s');
        }

        // 🚀 BƯỚC 3: Ghép thành câu lệnh SQL động an toàn tuyệt đối
        $col_names_string = implode(', ', $insert_cols);
        $placeholders = implode(', ', array_fill(0, count($insert_vals), '?'));
        
        $sql = "INSERT INTO documents ($col_names_string) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($insert_vals);
        
        // Thành công!
        $redirect_url = ($role === 'admin') ? '/LAPTRINHWEB/frontend/admin/documents.php' : '/LAPTRINHWEB/frontend/user/document.php';
        $msg = ($role === 'admin') ? 'Phát hành tài liệu thành công!' : 'Đã gửi duyệt tài liệu! Vui lòng chờ Admin phê duyệt.';
        
        echo "<script>alert('$msg'); window.location.href = '$redirect_url';</script>";
        exit();
        
    } catch (PDOException $e) {
        // 🚨 DEBUG TẬN RĂNG: Nếu DB vẫn báo lỗi, nó sẽ in ra luôn danh sách cột thực tế của sếp để sếp dễ bắt bệnh
        $actual_columns = implode(', ', $cols ?? []);
        $error_msg = "Lỗi Database: " . addslashes($e->getMessage()) . "\\n\\n=> CÁC CỘT HIỆN CÓ TRONG DB CỦA BẠN LÀ: [ " . $actual_columns . " ]";
        echo "<script>alert('$error_msg'); window.history.back();</script>";
    }
}
?>