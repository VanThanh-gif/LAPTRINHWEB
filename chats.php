<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI StudyHub - Chatbot Trợ Lý</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .chat-container { max-width: 700px; margin: 30px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden; }
        .chat-header { background-color: #1b365d; color: white; padding: 15px; font-weight: bold; }
        .chat-box { height: 450px; overflow-y: auto; padding: 20px; background-color: #f8f9fa; }
        .message { margin-bottom: 15px; max-width: 80%; padding: 10px 15px; border-radius: 15px; font-size: 15px; line-height: 1.4; }
        .message.user { background-color: #0d6efd; color: white; margin-left: auto; border-bottom-right-radius: 2px; }
        .message.bot { background-color: #e9ecef; color: #333; margin-right: auto; border-bottom-left-radius: 2px; white-space: pre-wrap; }
    </style>
</head>
<body>

<div class="container">
    <div class="chat-container">
        <div class="chat-header text-center">
            🤖 TRỢ LÝ AI - AISTUDYHUB
        </div>
        
        <div class="chat-box" id="chatBox">
            <div class="message bot">Xin chào! Tôi là trợ lý AI của dự án AIStudyHub. Bạn cần tôi hỗ trợ tìm kiếm tài liệu hay giải đáp kiến thức gì hôm nay?</div>
        </div>
        
        <div class="p-3 border-top bg-white">
            <form id="chatForm" class="d-flex">
                <input type="text" id="userInput" class="form-control me-2" placeholder="Nhập câu hỏi của bạn vào đây..." required autocomplete="off">
                <button type="submit" class="btn btn-primary px-4" id="sendBtn">Gửi</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('chatForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Chặn không cho trang web bị reload lại khi submit form
    
    const inputField = document.getElementById('userInput');
    const messageText = inputField.value.trim();
    if (!messageText) return;
    
    const chatBox = document.getElementById('chatBox');
    const sendBtn = document.getElementById('sendBtn');
    
    // 1. Hiển thị tin nhắn của User lên màn hình chat (Bong bóng màu xanh bên phải)
    const userMessageDiv = document.createElement('div');
    userMessageDiv.className = 'message user';
    userMessageDiv.innerText = messageText;
    chatBox.appendChild(userMessageDiv);
    
    // Xóa chữ trong ô nhập và cuộn khung chat xuống dưới cùng
    inputField.value = '';
    chatBox.scrollTop = chatBox.scrollHeight;
    
    // Vô hiệu hóa nút gửi tạm thời trong lúc chờ AI trả lời
    sendBtn.disabled = true;
    sendBtn.innerText = '...';

    // 2. Tạo hiệu ứng bong bóng "Đang xử lý..." của Bot
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'message bot';
    loadingDiv.innerText = 'AI đang suy nghĩ...';
    chatBox.appendChild(loadingDiv);
    chatBox.scrollTop = chatBox.scrollHeight;

    // 3. Sử dụng Fetch API để gửi dữ liệu ngầm sang file chatbot_service.php bằng phương thức POST
    const formData = new FormData();
    formData.append('message', messageText);

    fetch('chatbot_service.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text()) // Nhận kết quả trả về dạng chữ thuần từ file PHP
    .then(data => {
        // Xóa chữ "AI đang suy nghĩ..." và thay bằng câu trả lời thật từ Gemini
        loadingDiv.innerText = data;
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(error => {
        console.error('Lỗi:', error);
        loadingDiv.innerText = 'Đã xảy ra lỗi kết nối mạng. Vui lòng thử lại!';
    })
    .finally(() => {
        // Bật lại nút gửi như bình thường
        sendBtn.disabled = false;
        sendBtn.innerText = 'Gửi';
    });
});
</script>

</body>
</html>