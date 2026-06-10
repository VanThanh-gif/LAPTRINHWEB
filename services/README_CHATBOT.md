# 🤖 PHÂN HỆ: TRỢ LÝ HỌC TẬP THÔNG MINH (AI CHATBOT)
* **Thành viên thực hiện:** Nguyễn Văn Tài (Thành viên 3)
* **Vai trò nhiệm vụ:** Nghiên cứu tích hợp AI, thiết kế giao diện động, xây dựng hệ thống quản lý đa phiên thoại và lịch sử cuộc trò chuyện.

---

## 🚀 Các tính năng đã hoàn thiện và chạy mượt mà 100%
1. **Premium UI/UX Core:** Giao diện tối tân (Cinematic Dark Mode), tích hợp sẵn hệ thống 4 thẻ gợi ý học tập thông minh (Quick Prompts).
2. **Typing Effect & Auto-Scroll:** Hiệu ứng chữ chạy từ từ sinh động như ChatGPT thật. Hệ thống tự động nhận diện cuộn thông minh (Chỉ cuộn xuống đáy khi người dùng ở đáy, cho phép người dùng cuộn ngược lên đọc tài liệu cũ không bị giật màn hình).
3. **Stop Generating (Dừng phản hồi):** Tính năng cho phép người dùng bấm nút tạm dừng AI phản hồi ngay lập tức để chuyển câu hỏi khác.
4. **Đa phiên hội thoại (Multi-Sessions):** Cho phép bấm "Đoạn chat mới" để tạo một luồng chat trống hoàn toàn riêng biệt. Tự động đổi tên tiêu đề đoạn chat trên Sidebar theo 18 ký tự đầu của câu hỏi đầu tiên.
5. **Giải thuật phòng thủ bao sập mạng (Fallback Multi-Model):** Tự động phát hiện lỗi hệ thống từ Google AI Studio (Lỗi 503 Quá tải). Nếu model chính `gemini-2.5-flash` bận, hệ thống tự động cấu hình nhảy sang gọi `gemini-1.5-pro` hoặc `gemini-1.5-flash` để lấy bằng được câu trả lời cho sinh viên.

---

## 🗄️ Kiến trúc mã nguồn & Luồng dữ liệu

### 1. Cấu trúc thư mục phân hệ:
```text
LAPTRINHWEB/
└── services/
    ├── chats.php            # Giao diện chính, xử lý bắt sự kiện và render DOM động (Frontend)
    ├── chatbot_service.php  # Xử lý cổng cURL kết nối API Google, quản lý giải thuật Fallback và MySQL (Backend)
    └── README_CHATBOT.md    # Tài liệu hướng dẫn, đặc tả phân hệ của Thành viên 3