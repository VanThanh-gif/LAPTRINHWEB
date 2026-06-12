<?php
// 🌟 BƯỚC QUYẾT ĐỊNH: Bật tính năng Session lên đầu file để đọc thông tin đăng nhập của User
session_start(); 

// 1. Nhúng file cấu hình riêng tư để lấy API Key
require_once __DIR__ . '/config.php';
$api_key = $config_api_key; 

// Kết nối thông suốt MySQL cổng 3307 của Tài
$conn = new mysqli("127.0.0.1", "root", "", "aistudyhub", 3307);
if ($conn->connect_error) {
    echo json_encode(["error" => "Lỗi kết nối database: " . $conn->connect_error]);
    exit;
}

// Lấy ID người dùng đang đăng nhập thực tế, nếu chưa đăng nhập (test local) thì mặc định bằng 1
$current_user = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 1;

// CHỨC NĂNG: Xóa toàn bộ 1 đoạn chat (Chỉ xóa của chính người đang đăng nhập)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_session') {
    $session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : 0;
    
    if ($session_id > 0) {
        // Xóa tin nhắn chi tiết (Bảo mật: Phải khớp đúng user_id đang đăng nhập)
        $stmt1 = $conn->prepare("DELETE FROM chat_history WHERE session_id = ? AND user_id = ?");
        $stmt1->bind_param("ii", $session_id, $current_user);
        $stmt1->execute();
        $stmt1->close();

        // Xóa tiêu đề đoạn chat ở sidebar
        $stmt2 = $conn->prepare("DELETE FROM chat_sessions WHERE id = ? AND user_id = ?");
        $stmt2->bind_param("ii", $session_id, $current_user);
        
        if ($stmt2->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
        $stmt2->close();
    } else {
        echo json_encode(["success" => false, "error" => "ID không hợp lệ!"]);
    }
    $conn->close();
    exit;
}

// Chức năng 1: Tạo một Đoạn chat mới trong database (Gắn đúng ID người đang đăng nhập)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create_session') {
    $title = "Đoạn chat mới " . date('H:i:s');
    $stmt = $conn->prepare("INSERT INTO chat_sessions (user_id, title) VALUES (?, ?)");
    $stmt->bind_param("is", $current_user, $title);
    $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode(["session_id" => $new_id, "title" => $title]);
    exit;
}

// Chức năng 2: Lấy danh sách Đoạn chat để hiện lên Sidebar (Chỉ lấy của người đang đăng nhập)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_sessions') {
    $result = $conn->query("SELECT id, title FROM chat_sessions WHERE user_id = $current_user ORDER BY id DESC");
    $sessions = [];
    while ($row = $result->fetch_assoc()) { $sessions[] = $row; }
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($sessions);
    exit;
}

// Chức năng 3: Lấy lịch sử tin nhắn cũ của 1 Đoạn chat (Chỉ lấy nếu thuộc về người đang đăng nhập)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_history') {
    $session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;
    
    $stmt = $conn->prepare("SELECT message, response FROM chat_history WHERE user_id = ? AND session_id = ? ORDER BY id ASC");
    $stmt->bind_param("ii", $current_user, $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) { $history[] = $row; }
    $stmt->close();
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode($history);
    exit;
}

// Chức năng 4: Xử lý nhận tin nhắn, gọi API Gemini và lưu MySQL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : 0;

    if (empty($message) || $session_id === 0) {
        echo json_encode(["error" => "Dữ liệu không hợp lệ!"]);
        exit;
    }

    $models_to_try = ["gemini-2.5-flash", "gemini-1.5-pro", "gemini-1.5-flash"];
    $bot_reply = null;
    $error_response = null;

    foreach ($models_to_try as $current_model) {
        $url = "https://generativelanguage.googleapis.com/v1/models/{$current_model}:generateContent?key=" . $api_key;
        $data = ["contents" => [["parts" => [["text" => $message]]]]];
        $json_data = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code === 200) {
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $bot_reply = $result['candidates'][0]['content']['parts'][0]['text'];
                break; 
            }
        } else {
            $error_response = $response;
        }
    }

    if ($bot_reply !== null) {
        $check_count = $conn->query("SELECT id FROM chat_history WHERE session_id = $session_id AND user_id = $current_user");
        if ($check_count->num_rows === 0) {
            $short_title = mb_substr($message, 0, 18) . "...";
            $conn->query("UPDATE chat_sessions SET title = '$short_title' WHERE id = $session_id AND user_id = $current_user");
        }

        $stmt = $conn->prepare("INSERT INTO chat_history (user_id, session_id, message, response) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $current_user, $session_id, $message, $bot_reply);
        $stmt->execute();
        $stmt->close();
        
        echo $bot_reply;
    } else {
        echo "Hệ thống Google AI phản hồi lỗi. Chi tiết: " . $error_response;
    }
    $conn->close();
}
?>