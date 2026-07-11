# 🚀 AI Study Hub - Nền tảng Kho Tài liệu Trực tuyến

Dự án website quản lý và chia sẻ tài liệu học tập, tích hợp các chức năng quản trị mạnh mẽ.

## 🛠️ Yêu cầu hệ thống (Prerequisites)

Để chạy được dự án này, máy tính của bạn cần cài đặt:

- **XAMPP** (hoặc WAMP, Laragon) hỗ trợ Apache và MySQL.
- **PHP** phiên bản >= 8.0
- Trình duyệt web (Chrome, Edge, Safari...)

---

## ⚙️ Hướng dẫn Cài đặt & Khởi chạy (Installation Guide)

**Bước 1: Cấu hình mã nguồn**

1. Copy toàn bộ thư mục `LAPTRINHWEB` (chứa mã nguồn) dán vào thư mục `htdocs` của XAMPP.
   _(Đường dẫn chuẩn: `C:\xampp\htdocs\LAPTRINHWEB`)_

**Bước 2: Khởi động Server**

1. Mở **XAMPP Control Panel**.
2. Bấm `Start` ở 2 module: **Apache** và **MySQL**.

**Bước 3: Thiết lập Cơ sở dữ liệu (Database)**

1. Mở trình duyệt, truy cập vào: `http://localhost/phpmyadmin`
2. Tạo một Database mới với tên là: `aistudyhub` (Collation: `utf8mb4_unicode_ci`).
3. Chọn tab **Import (Nhập)** -> Tải lên file cơ sở dữ liệu gốc của dự án (ví dụ: `aistudyhub.sql` nằm trong thư mục project) và bấm **Go (Thực hiện)**.

**Bước 4: Kết nối Database**

- Kiểm tra file `config/connectdb.php` để đảm bảo thông tin đăng nhập database khớp với máy của bạn:
  - Máy chủ: `localhost`
  - Tên DB: `aistudyhub`
  - User: `root`
  - Mật khẩu: `(để trống)`

**Bước 5: Trải nghiệm Website**

- Truy cập trang chủ (User): `http://localhost/LAPTRINHWEB/frontend/user/document.php`
- Truy cập khu vực Quản trị (Admin): `http://localhost/LAPTRINHWEB/frontend/admin/dashboard.php`

---

## 🔑 Tài khoản Test mặc định

Sử dụng các tài khoản dưới đây để test các chức năng phân quyền của hệ thống:

**1. Tài khoản Super Admin (Quản trị viên):**

- Email: `admin@aistudyhub.com`
- Mật khẩu: `admin123` (Hoặc mật khẩu bạn đã thiết lập)

---

## 📁 Cấu trúc thư mục chính (Folder Structure)

```text
LAPTRINHWEB/
│
├── config/                 # File cấu hình kết nối CSDL (connectdb.php)
├── includes/               # Các phần giao diện dùng chung (header, navbar, footer)
├── frontend/               # Giao diện hiển thị
│   ├── admin/              # Khu vực quản trị (Dashboard, Quản lý User/Tài liệu)
│   ├── user/               # Khu vực người dùng (Xem và tải tài liệu)
│   └── guest/              # Khu vực khách (Đăng nhập, Đăng ký)
├── backend/                # Nơi chứa các file xử lý logic (nếu có tách riêng)
└── README.md               # Hướng dẫn chạy dự án
```
