<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database chung của nhóm (nhảy ngược ra 2 cấp)
require_once __DIR__ . '/../../config/connectdb.php';
// Gọi file cấu hình chứa API Key nằm cùng thư mục với file giao diện
require_once __DIR__ . '/../../frontend/services/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Chưa đăng nhập hệ thống']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu tin nhắn từ giao diện gửi lên
$input = json_decode(file_get_contents('php://input'), true);
$message = isset($input['message']) ? trim($input['message']) : '';

if (empty($message)) {
    echo json_encode(['error' => 'Tin nhắn không được để trống']);
    exit();
}

// 1. Lưu tin nhắn của Người dùng vào Database
try {
    $stmt = $conn->prepare("INSERT INTO chatbot_history (user_id, sender, message, created_at) VALUES (?, 'user', ?, NOW())");
    $stmt->execute([$user_id, $message]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Lỗi lưu database: ' . $e->getMessage()]);
    exit();
}

// 2. Gọi API Gemini để lấy câu trả lời
$api_key = isset($config_api_key) ? $config_api_key : '';
if (empty($api_key)) {
    echo json_encode(['error' => 'Thiếu Gemini API Key trong config.php']);
    exit();
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $api_key;

$data = [
    "contents" => [
        [
            "parts" => [
                ["text" => $message]
            ]
        ]
    ]
    // Tài có thể thêm phần systemInstruction ở đây nếu muốn chatbot thông minh hơn nhé
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tránh lỗi SSL trên XAMPP local

$response = curl_exec($ch);
$curl_error = curl_error($ch);
curl_close($ch);

if ($curl_error) {
    echo json_encode(['error' => 'CURL Lỗi: ' . $curl_error]);
    exit();
}

$response_data = json_decode($response, true);
$bot_reply = "";

if (isset($response_data['candidates'][0]['content']['parts'][0]['text'])) {
    $bot_reply = $response_data['candidates'][0]['content']['parts'][0]['text'];
} else {
    $bot_reply = "Xin lỗi, chatbot đang gặp sự cố kết nối API. Vui lòng thử lại sau!";
}

// 3. Lưu câu trả lời của Bot vào Database
try {
    $stmt = $conn->prepare("INSERT INTO chatbot_history (user_id, sender, message, created_at) VALUES (?, 'bot', ?, NOW())");
    $stmt->execute([$user_id, $bot_reply]);
    
    // Trả kết quả về cho giao diện hiển thị
    echo json_encode(['reply' => $bot_reply]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Lỗi lưu câu trả lời: ' . $e->getMessage()]);
}