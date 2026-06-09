<?php
// 1. Cấu hình API Key của bạn
$api_key = "AQ.Ab8RN6JG8AQpAAk019bssaUJlifMWNzpouXFuMHt_uQhupuqgw"; 

// 2. Kiểm tra xem người dùng có gửi câu hỏi lên không (Dùng phương thức POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);

    if (empty($message)) {
        echo json_encode(["error" => "Câu hỏi không được để trống!"]);
        exit;
    }

    // 3. Đường dẫn API chính thức của Google Gemini
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key;

    // 4. Đóng gói dữ liệu gửi lên AI theo định dạng JSON
    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $message]
                ]
            ]
        ]
    ];
    $json_data = json_encode($data);

    // 5. Khởi tạo kết nối cURL để gửi dữ liệu ngầm trong PHP
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    // 6. Thực thi gửi và nhận phản hồi từ Google
    $response = curl_exec($ch);

    // Kiểm tra lỗi mạng/cURL nếu có
    if (curl_errno($ch)) {
        echo json_encode(["error" => "Lỗi kết nối mạng: " . curl_error($ch)]);
    } else {
        // 7. Giải mã kết quả trả về từ Google
        $result = json_decode($response, true);
        
        // Bóc tách lấy đúng chuỗi văn bản câu trả lời của AI
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $bot_reply = $result['candidates'][0]['content']['parts'][0]['text'];
            
            // Trả về kết quả dạng văn bản thuần cho Frontend hiển thị
            echo $bot_reply;
        } else {
            echo "API lỗi hoặc Key không đúng. Chi tiết: " . $response;
        }
    }
    curl_close($ch);
} else {
    // Nếu truy cập trực tiếp file này mà không gửi câu hỏi bằng POST
    echo "Hệ thống Backend Chatbot hoạt động ổn định. Sẵn sàng nhận tin nhắn.";
}
?>