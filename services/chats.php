<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI StudyHub - Trợ Lý Học Tập Thông Minh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-bg: #09090b;
            --main-bg: #141416;
            --card-bg: #1e1e21;
            --input-bg: #222226;
            --text-main: #f4f4f5;
            --text-sub: #a1a1aa;
            --accent-color: #3b82f6;
            --accent-gradient: linear-gradient(135deg, #3b82f6, #8b5cf6);
            --user-msg-bg: #2563eb;
            --bot-msg-bg: #1f1f23;
        }

        * { font-family: 'Plus Jakarta Sans', sans-serif; box-sizing: border-box; }
        body { background-color: var(--main-bg); color: var(--text-main); height: 100vh; margin: 0; display: flex; overflow: hidden; }

        /* --- SIDEBAR TRÁI ĐẲNG CẤP PREMIUM --- */
        .sidebar {
            width: 280px;
            background-color: var(--sidebar-bg);
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #27272a;
            z-index: 10;
        }
        .new-chat-btn {
            background: var(--accent-gradient);
            border: none;
            color: #fff;
            border-radius: 12px;
            padding: 14px 18px;
            text-align: left;
            transition: all 0.25s ease;
            font-size: 0.92rem;
            font-weight: 600;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .new-chat-btn:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4); }
        .chat-list { margin-top: 28px; flex-grow: 1; overflow-y: auto; }
        .chat-list::-webkit-scrollbar { width: 3px; }
        .chat-list::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }
        
        .chat-item {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 0.88rem;
            color: var(--text-sub);
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        .chat-item i { margin-right: 12px; font-size: 0.95rem; color: #52525b; transition: color 0.2s; }
        .chat-item:hover { background-color: #18181b; color: #fff; }
        .chat-item:hover i { color: var(--accent-color); }
        .chat-item.active { background-color: #1e1e21; color: #fff; font-weight: 500; box-shadow: inset 3px 0 0 var(--accent-color); }
        .chat-item.active i { color: var(--accent-color); }

        /* --- BỐ CỤC KHUNG CHAT CHÍNH --- */
        .chat-main { flex-grow: 1; display: flex; flex-direction: column; height: 100vh; position: relative; background-color: var(--main-bg); }
        .chat-header {
            background-color: rgba(20, 20, 22, 0.7);
            backdrop-filter: blur(20px);
            color: #fff;
            padding: 20px 40px;
            font-weight: 600;
            font-size: 1.1rem;
            border-bottom: 1px solid #27272a;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-box { flex-grow: 1; overflow-y: auto; padding: 40px 18% 160px 18%; scroll-behavior: smooth; }
        .chat-box::-webkit-scrollbar { width: 5px; }
        .chat-box::-webkit-scrollbar-thumb { background: #27272a; border-radius: 6px; }

        /* --- MÀN HÌNH GỢI Ý KHI ĐOẠN CHAT TRỐNG --- */
        .welcome-container { text-align: center; margin-top: 4vh; animation: fadeIn 0.6s ease; }
        .welcome-logo { 
            width: 70px; height: 70px; background: var(--accent-gradient); 
            border-radius: 20px; display: flex; align-items: center; justify-content: center; 
            font-size: 2.2rem; margin: 0 auto 24px auto; color: white;
            box-shadow: 0 10px 25px rgba(5b, 130, 246, 0.25);
        }
        .welcome-container h4 { font-weight: 700; font-size: 1.6rem; color: #fff; margin-bottom: 10px; }
        .suggest-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 40px; }
        .suggest-card { 
            background-color: var(--card-bg); border: 1px solid #27272a; 
            border-radius: 16px; padding: 20px; text-align: left; cursor: pointer; 
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .suggest-card:hover { border-color: #4b5563; background-color: #242428; transform: translateY(-3px); box-shadow: 0 12px 20px rgba(0,0,0,0.2); }
        .suggest-card h5 { font-size: 0.95rem; margin-bottom: 6px; color: #fff; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .suggest-card p { font-size: 0.84rem; margin: 0; color: var(--text-sub); line-height: 1.4; }

        /* --- BONG BÓNG TIN NHẮN TỐI ƯU --- */
        .message-wrapper { display: flex; margin-bottom: 32px; align-items: flex-start; opacity: 0; animation: fadeIn 0.35s forwards; }
        @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } from { opacity: 0; transform: translateY(10px); } }
        
        .avatar { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.05rem; margin-right: 20px; flex-shrink: 0; }
        .bot-avatar { background: #10a37f; color: white; box-shadow: 0 4px 12px rgba(16, 163, 127, 0.2); }
        .user-avatar { background: var(--accent-gradient); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); }

        .message { max-width: 80%; font-size: 1.02rem; line-height: 1.65; color: var(--text-main); }
        
        /* Tin nhắn của sinh viên gõ */
        .user-wrapper { flex-direction: row-reverse; }
        .user-wrapper .avatar { margin-right: 0; margin-left: 20px; }
        .user-wrapper .message { 
            background-color: var(--user-msg-bg); 
            padding: 14px 22px; 
            border-radius: 20px 20px 4px 20px; 
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.15);
            color: #fff;
        }

        /* Tin nhắn của Chatbot AI trả lời */
        .bot-wrapper .message {
            background-color: var(--bot-msg-bg);
            padding: 16px 24px;
            border-radius: 20px 20px 20px 4px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: 1px solid #27272a;
        }
        .bot-message { white-space: pre-wrap; word-break: break-word; }

        /* --- THANH KHUNG NHẬP CHAT NỔI --- */
        .chat-input-container { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, var(--main-bg) 45%); padding: 20px 18% 40px 18%; }
        .chat-input-area { background-color: var(--input-bg); border: 1px solid #27272a; border-radius: 18px; padding: 14px 22px; display: flex; align-items: center; box-shadow: 0 12px 32px rgba(0,0,0,0.35); transition: all 0.25s ease; }
        .chat-input-area:focus-within { border-color: #52525b; background-color: #26262b; box-shadow: 0 12px 32px rgba(59, 130, 246, 0.1); }
        .chat-input-area input { background: transparent; border: none; color: var(--text-main); flex-grow: 1; padding: 4px; font-size: 1.05rem; }
        .chat-input-area input:focus { outline: none; }
        .send-btn { background: #27272a; border: none; color: #a1a1aa; width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .send-btn:hover { background: #fff; color: #000; transform: scale(1.03); }

        /* Hiệu ứng 3 dấu chấm loading nhấp nháy */
        .typing-loader { display: inline-block; font-weight: bold; animation: blink 1.4s infinite both; color: var(--accent-color); }
        .typing-loader:nth-child(2) { animation-delay: .2s; }
        .typing-loader:nth-child(3) { animation-delay: .4s; }
        @keyframes blink { 0%, 100% { opacity: .2; } 20% { opacity: 1; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <button class="new-chat-btn" onclick="startNewChatSession()">
            <span><i class="fa fa-plus me-2"></i> Đoạn chat mới</span>
            <i class="fa-regular fa-pen-to-square" style="opacity: 0.7;"></i>
        </button>
        <div class="chat-list" id="sessionsContainer"></div>
        <div class="text-muted text-center pt-2" style="font-size: 0.78rem; border-top: 1px solid #27272a; color: var(--text-sub) !important;">
            <i class="fa-solid fa-circle-user text-success me-1"></i> Nguyễn Văn Tài - Nhóm 8
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <div>
                <i class="fa-solid fa-wand-magic-sparkles text-primary me-2"></i> 
                <span style="font-weight: 600; letter-spacing: -0.3px;">AI StudyHub Premium</span>
            </div>
            <span class="badge bg-dark text-success border border-secondary px-3 py-2" style="font-size: 0.75rem; border-radius: 8px;"><i class="fa-solid fa-bolt text-warning me-1"></i> Gemini Multi-Model v1</span>
        </div>
        
        <div class="chat-box" id="chatBox"></div>
        
        <div class="chat-input-container">
            <div class="chat-input-area">
                <input type="text" id="userInput" placeholder="Hỏi câu hỏi học tập tại đây..." onkeypress="handleKeyPress(event)">
                <button class="send-btn" onclick="sendMessage()"><i class="fa-solid fa-arrow-up"></i></button>
            </div>
        </div>
    </div>

<script>
let currentSessionId = 0;
let isTyping = false;
let typingTimeout = null;

function handleKeyPress(event) {
    if (event.key === 'Enter') sendMessage();
}

function formatBotResponse(text) {
    if (!text) return "";
    let formatted = text.replace(/\n/g, "<br>");
    if (formatted.includes("```")) {
        let parts = formatted.split("```");
        for (let i = 1; i < parts.length; i += 2) {
            parts[i] = "<pre style='background-color: #09090b; color: #38bdf8; padding: 14px; border-radius: 8px; font-family: monospace; overflow-x: auto; margin: 10px 0;'>" + parts[i].replace(/^[a-zA-Z]+<br>/, "") + "</pre>";
        }
        return parts.join("");
    }
    return formatted;
}

function appendMessage(text, isUser, triggerTypingEffect = false) {
    const chatBox = document.getElementById('chatBox');
    const wrapper = document.createElement('div');
    wrapper.className = isUser ? 'message-wrapper user-wrapper' : 'message-wrapper bot-wrapper';
    
    const avatar = document.createElement('div');
    avatar.className = isUser ? 'avatar user-avatar' : 'avatar bot-avatar';
    avatar.innerHTML = isUser ? '<i class="fa-solid fa-user"></i>' : '<i class="fa-solid fa-robot"></i>';
    
    const msgDiv = document.createElement('div');
    msgDiv.className = 'message';
    
    wrapper.appendChild(avatar);
    wrapper.appendChild(msgDiv);
    chatBox.appendChild(wrapper);
    
    // Kiểm tra nếu người dùng cuộn lên trên xem tài liệu thì không ép cuộn đáy
    const isAtBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 100;
    if (isAtBottom || isUser) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    if (!isUser && triggerTypingEffect) {
        isTyping = true;
        toggleStopButton(true); // Biến đổi nút Gửi thành nút Dừng
        let index = 0;
        msgDiv.innerHTML = "";
        
        let rawText = text;
        function typeWriter() {
            if (index < rawText.length) {
                msgDiv.textContent += rawText.charAt(index);
                index++;
                
                if (chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 150) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
                
                typingTimeout = setTimeout(typeWriter, 5);
            } else {
                msgDiv.innerHTML = formatBotResponse(rawText);
                isTyping = false;
                toggleStopButton(false);
            }
        }
        typeWriter();
    } else {
        msgDiv.innerHTML = isUser ? text : formatBotResponse(text);
    }
    
    return wrapper;
}

// Chức năng biến nút Gửi -> Dừng phản hồi giống ChatGPT
function toggleStopButton(show) {
    const sendBtn = document.querySelector('.send-btn');
    if (show) {
        sendBtn.innerHTML = '<i class="fa-solid fa-stop text-danger"></i>';
        sendBtn.setAttribute('onclick', 'stopGenerating()');
        sendBtn.style.background = '#3f2225';
    } else {
        sendBtn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
        sendBtn.setAttribute('onclick', 'sendMessage()');
        sendBtn.style.background = '#27272a';
    }
}

function stopGenerating() {
    if (typingTimeout) {
        clearTimeout(typingTimeout);
        isTyping = false;
        toggleStopButton(false);
        
        const botMessages = document.querySelectorAll('.bot-message-last .message');
        if(botMessages.length > 0) {
            const lastMsg = botMessages[botMessages.length - 1];
            lastMsg.innerHTML = formatBotResponse(lastMsg.textContent + " ⏹️ (Đã dừng phản hồi)");
        }
        loadChatSessions();
    }
}

function loadChatSessions() {
    fetch('chatbot_service.php?action=get_sessions')
    .then(response => response.json())
    .then(sessions => {
        const container = document.getElementById('sessionsContainer');
        container.innerHTML = '';
        
        if(sessions.length === 0) {
            startNewChatSession();
            return;
        }

        sessions.forEach(session => {
            const item = document.createElement('div');
            item.className = 'chat-item' + (session.id === currentSessionId ? ' active' : '');
            item.innerHTML = `<i class="fa-regular fa-comment-dots"></i> ${session.title}`;
            item.onclick = () => { if(!isTyping) selectSession(session.id); };
            container.appendChild(item);
        });

        if(currentSessionId === 0 && sessions.length > 0) {
            selectSession(sessions[0].id);
        }
    });
}

function quickAsk(question) {
    if(isTyping) return;
    document.getElementById('userInput').value = question;
    sendMessage();
}

function selectSession(sessionId) {
    currentSessionId = sessionId;
    document.querySelectorAll('.chat-item').forEach(el => el.classList.remove('active'));
    loadChatSessions();

    const chatBox = document.getElementById('chatBox');
    chatBox.innerHTML = '';

    fetch(`chatbot_service.php?action=get_history&session_id=${sessionId}`)
    .then(response => response.json())
    .then(history => {
        if(history.length === 0) {
            chatBox.innerHTML = `
                <div class="welcome-container">
                    <div class="welcome-logo"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h4>Tôi có thể giúp gì cho việc học của bạn hôm nay?</h4>
                    <p style="color: var(--text-sub); font-size: 0.9rem;">Hệ thống kết nối trực tiếp cơ sở dữ liệu và AI để giải đáp kiến thức.</p>
                    <div class="suggest-grid">
                        <div class="suggest-card" onclick="quickAsk('Giải thích khái niệm lập trình hướng đối tượng OOP ngắn gọn kèm ví dụ C++')">
                            <h5><i class="fa-solid fa-lightbulb text-warning"></i> Giải thích khái niệm</h5>
                            <p>Hỏi về OOP, Cấu trúc dữ liệu, Thuật toán hay Database...</p>
                        </div>
                        <div class="suggest-card" onclick="quickAsk('Kiểm tra và sửa lỗi đoạn code PHP kết nối MySQL cổng 3307 sau đây')">
                            <h5><i class="fa-solid fa-code text-info"></i> Sửa lỗi Code nhanh</h5>
                            <p>Dán đoạn code lỗi của bạn vào đây để AI tìm lỗi cú pháp.</p>
                        </div>
                        <div class="suggest-card" onclick="quickAsk('Viết dàn ý bài luận tiếng anh chủ đề công nghệ trong tương lai cấp độ B1')">
                            <h5><i class="fa-solid fa-pen-nib text-primary"></i> Hỗ trợ bài tiếng Anh</h5>
                            <p>Tạo cấu trúc bài viết ôn thi Midterm Tiếng Anh B1.3</p>
                        </div>
                        <div class="suggest-card" onclick="quickAsk('Tóm tắt các lệnh Git cơ bản cần nhớ khi làm việc nhóm đồ án')">
                            <h5><i class="fa-solid fa-code-branch text-success"></i> Lệnh Git làm việc nhóm</h5>
                            <p>Xem nhanh cú pháp commit, push, pull khi làm đồ án web.</p>
                        </div>
                    </div>
                </div>`;
        } else {
            history.forEach(chat => {
                appendMessage(chat.message, true);
                appendMessage(chat.response, false, false);
            });
        }
    });
}

function startNewChatSession() {
    if(isTyping) return;
    fetch('chatbot_service.php?action=create_session', { method: 'POST' })
    .then(response => response.json())
    .then(data => {
        currentSessionId = data.session_id;
        loadChatSessions();
        selectSession(data.session_id);
    });
}

function sendMessage() {
    if(isTyping) return;
    const inputField = document.getElementById('userInput');
    const messageText = inputField.value.trim();
    if (messageText === '' || currentSessionId === 0) return;

    appendMessage(messageText, true);
    inputField.value = '';

    const loadingText = 'AI đang xử lý dữ liệu <span class="typing-loader">.</span><span class="typing-loader">.</span><span class="typing-loader">.</span>';
    const loadingWrapper = appendMessage(loadingText, false);
    toggleStopButton(true);

    const formData = new FormData();
    formData.append('message', messageText);
    formData.append('session_id', currentSessionId);

    fetch('chatbot_service.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        loadingWrapper.remove(); 
        const botMsgWrapper = appendMessage(data, false, true);
        botMsgWrapper.classList.add('bot-message-last');
    })
    .catch(error => {
        toggleStopButton(false);
        loadingWrapper.querySelector('.message').textContent = 'Hệ thống bận. Vui lòng thử lại!';
    });
}

window.onload = function() {
    loadChatSessions();
};
</script>

</body>
</html>