<?php
// 1. Cấu hình API Key Gemini chính chủ của Tài đã cấp
$api_key = "AQ.Ab8RN6KkOlhqxn4kYlgGJs45VLWygUUNia1MuMxVP45Qw8fr-A"; // Xóa cái key thật đi để qua mặt GitHub
// Kết nối thông suốt MySQL cổng 3307 của Tài
$conn = new mysqli("127.0.0.1", "root", "", "aistudyhub", 3307);
if ($conn->connect_error) {
    echo json_encode(["error" => "Lỗi kết nối database: " . $conn->connect_error]);
    exit;
}

// Chức năng 1: Tạo một Đoạn chat mới trong database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'create_session') {
    $title = "Đoạn chat mới " . date('H:i:s');
    $stmt = $conn->prepare("INSERT INTO chat_sessions (user_id, title) VALUES (1, ?)");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $new_id = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    
    header('Content-Type: application/json');
    echo json_encode(["session_id" => $new_id, "title" => $title]);
    exit;
}

// Chức năng 2: Lấy danh sách tất cả các Đoạn chat để hiện lên Sidebar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_sessions') {
    $result = $conn->query("SELECT id, title FROM chat_sessions WHERE user_id = 1 ORDER BY id DESC");
    $sessions = [];
    while ($row = $result->fetch_assoc()) { $sessions[] = $row; }
    $conn->close();
    header('Content-Type: application/json');
    echo json_encode($sessions);
    exit;
}

// Chức năng 3: Lấy toàn bộ lịch sử tin nhắn cũ của riêng 1 Đoạn chat được chọn
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_history') {
    $session_id = isset($_GET['session_id']) ? intval($_GET['session_id']) : 0;
    
    $stmt = $conn->prepare("SELECT message, response FROM chat_history WHERE user_id = 1 AND session_id = ? ORDER BY id ASC");
    $stmt->bind_param("i", $session_id);
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

// Chức năng 4: Xử lý nhận tin nhắn, gọi API song song bao nghẽn mạch (Fallback) và lưu MySQL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : 0;

    if (empty($message) || $session_id === 0) {
        echo json_encode(["error" => "Dữ liệu không hợp lệ!"]);
        exit;
    }

    // 🔥 THUẬT TOÁN PHÒNG THỦ BAO SẬP MẠNG GOOGLE (TỰ ĐỔI MODEL NẾU DÍNH 503)
    $models_to_try = [
        "gemini-2.5-flash", 
        "gemini-1.5-pro",   
        "gemini-1.5-flash"  
    ];

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
                break; // Có câu trả lời ngon lành -> thoát vòng lặp ngay!
            }
        } else {
            $error_response = $response;
        }
    }

    // Lưu dữ liệu nếu lấy được câu trả lời thành công
    if ($bot_reply !== null) {
        // Tự động lấy 18 ký tự đầu câu hỏi làm tiêu đề sidebar cho gọn đẹp
        $check_count = $conn->query("SELECT id FROM chat_history WHERE session_id = $session_id");
        if ($check_count->num_rows === 0) {
            $short_title = mb_substr($message, 0, 18) . "...";
            $conn->query("UPDATE chat_sessions SET title = '$short_title' WHERE id = $session_id");
        }

        // Thực hiện chèn lịch sử vào bảng MySQL
        $user_id = 1; 
        $stmt = $conn->prepare("INSERT INTO chat_history (user_id, session_id, message, response) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $user_id, $session_id, $message, $bot_reply);
        $stmt->execute();
        $stmt->close();
        
        echo $bot_reply;
    } else {
        // Nếu tất cả các mô hình đồng loạt nghẽn thật sự
        echo "Hệ thống Google AI đang quá tải. Chi tiết lỗi: " . $error_response;
    }
    $conn->close();
}
?>