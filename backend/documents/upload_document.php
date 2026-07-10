<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Bảo mật: Nếu chưa đăng nhập tài khoản User thì đẩy ra trang login
if (!isset($_SESSION['user_id'])) {
    header("Location: /AIStudyHub/Guest/frontend/auth/login.php");
    exit();
}

// Kết nối database an toàn từ thư mục gốc
require_once $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/config/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = (int)$_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    $status = 'pending'; // Trạng thái mặc định chờ Admin duyệt

    // Kiểm tra xem file đã được chọn từ máy tính chưa
    if (!isset($_FILES['document_file']) || $_FILES['document_file']['error'] !== UPLOAD_ERR_OK) {
        echo "<script>alert('Vui lòng chọn một file tài liệu hợp lệ từ máy tính của bạn!'); window.history.back();</script>";
        exit();
    }

    $file = $_FILES['document_file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    
    // Thư mục lưu trữ file thực tế trên ổ đĩa máy tính (htdocs/AIStudyHub/uploads/)
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/AIStudyHub/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Nếu chưa có thư mục uploads thì tự động tạo mới
    }

    // Đổi tên file sang chuỗi ngẫu nhiên để tránh trùng tên tệp tin trên máy chủ
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
    $targetFilePath = $uploadDir . $newFileName;

    // Các định dạng file tài liệu an toàn được phép tải lên
    $allowedTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt'];

    if (in_array($fileExtension, $allowedTypes)) {
        // Thực hiện di chuyển file vật lý từ máy tính vào thư mục dự án
        if (move_uploaded_file($fileTmpName, $targetFilePath)) {
            try {
                
                // --- THUẬT TOÁN TỰ ĐỘNG DÒ TÊN CỘT DATABASE ĐỂ TRÁNH LỖI UNKNOWN COLUMN ---
                $column_name = 'file_path'; // Tên mặc định giả định
                
                // Lấy danh sách toàn bộ tên các cột thực tế của bảng documents trong DB của bạn
                $q_columns = $conn->query("DESCRIBE documents")->fetchAll(PDO::FETCH_COLUMN);
                
                // Tự động kiểm tra xem cấu trúc bảng thực tế của bạn đang đặt tên cột lưu file là gì để khớp lệnh
                if (in_array('file_path', $q_columns)) {
                    $column_name = 'file_path';
                } elseif (in_array('file', $q_columns)) {
                    $column_name = 'file';
                } elseif (in_array('document_url', $q_columns)) {
                    $column_name = 'document_url';
                } elseif (in_array('path', $q_columns)) {
                    $column_name = 'path';
                } else {
                    // Nếu không tìm thấy cột lưu file quen thuộc, hệ thống sẽ tự lấy cột tùy chỉnh còn lại của bạn
                    $ignored = ['document_id', 'title', 'author', 'category_id', 'user_id', 'status', 'created_at', 'upload_date'];
                    foreach($q_columns as $col) {
                        if(!in_array($col, $ignored)) {
                            $column_name = $col;
                            break;
                        }
                    }
                }

                // Thực hiện câu lệnh INSERT SQL động cực kỳ an toàn
                $sql = "INSERT INTO documents (title, author, category_id, user_id, {$column_name}, status, created_at) 
                        VALUES (?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $conn->prepare($sql);
                $stmt->execute([$title, $author, $category_id, $user_id, $newFileName, $status]);

                // Thành công: Đưa về trang kho tài liệu của sinh viên
                echo "<script>alert('Tải tài liệu lên thành công! Vui lòng chờ Admin kiểm duyệt.'); window.location.href='/AIStudyHub/User/frontend/documents/document.php';</script>";
                exit();

            } catch (PDOException $e) {
                // Nếu lưu DB thất bại thì xóa file vật lý vừa tải lên để tránh rác ổ cứng
                if(file_exists($targetFilePath)) { unlink($targetFilePath); }
                die("Lỗi đồng bộ Cơ sở dữ liệu: " . $e->getMessage());
            }
        } else {
            echo "<script>alert('Không thể lưu file vào thư mục uploads trên máy chủ!'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "<script>alert('Định dạng file không hợp lệ! Chỉ cho phép tải lên file PDF, Word, PowerPoint, TXT.'); window.history.back();</script>";
        exit();
    }
} else {
    header("Location: /AIStudyHub/User/frontend/documents/upload.php");
    exit();
}