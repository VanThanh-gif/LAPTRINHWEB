<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Lấy tên hiển thị
$display_name = $_SESSION['username'] ?? (isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'Quản Trị Viên' : 'Thành Viên');

// Đường dẫn Chatbot
$chatbot_url = "/LAPTRINHWEB/frontend/services/chats.php"; 

// Link trang chủ thông minh
$home_url = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') 
            ? '/LAPTRINHWEB/frontend/admin/dashboard.php' 
            : '/LAPTRINHWEB/frontend/user/document.php';

// 🚀 CƠ CHẾ FIX LỖI 404 AVATAR: Tự động chuẩn hóa đường dẫn tuyệt đối
$avatar_url = '';
if (!empty($_SESSION['avatar'])) {
    // Nếu đường dẫn lưu trong Session đã có sẵn tên thư mục gốc /LAPTRINHWEB/
    if (strpos($_SESSION['avatar'], '/LAPTRINHWEB/') === 0) {
        $avatar_url = $_SESSION['avatar'];
    } else {
        // Nếu chỉ lưu dạng 'uploads/avatars/file.jpg', hệ thống tự động bù thêm thư mục gốc vào
        $avatar_url = '/LAPTRINHWEB/' . ltrim($_SESSION['avatar'], '/');
    }
}

// Ảnh chữ dự phòng nếu ảnh thật bị trống hoặc lỗi load
$fallback_avatar = "https://ui-avatars.com/api/?name=" . urlencode($display_name) . "&background=random&color=fff&bold=true";
?>

<nav class="navbar navbar-expand-lg" style="background: linear-gradient(135deg, #4318FF 0%, #39B8FF 100%); padding: 15px 0; box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
    <div class="container">
        <a class="navbar-brand text-white fw-bold fs-4 d-flex align-items-center" href="<?= $home_url ?>">
            <i class="bi bi-cpu-fill me-2" style="color: #39B8FF;"></i>AIStudyHub
        </a>
        
        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">
                <li class="nav-item me-2">
                    <a class="nav-link text-white fw-semibold px-3 py-2" href="/LAPTRINHWEB/frontend/<?= (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'admin/documents.php' : 'user/document.php' ?>">
                        <i class="bi bi-folder2-open me-1"></i> Kho Tài Liệu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white fw-semibold px-3 py-2" href="<?= $chatbot_url ?>">
                        <i class="bi bi-robot me-1"></i> Chatbot AI
                    </a>
                </li>
            </ul>
            

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle text-white fw-medium" href="#" data-bs-toggle="dropdown">
                        <img src="<?= !empty($avatar_url) ? $avatar_url : $fallback_avatar ?>" 
                             onerror="this.onerror=null; this.src='<?= $fallback_avatar ?>';" 
                             alt="Avatar" class="rounded-circle me-2 border border-2 border-white bg-white" style="width: 38px; height: 38px; object-fit: cover;">
                        <span>Chào, <?= htmlspecialchars($display_name) ?></span>

            <div class="dropdown">
              <a class="text-white text-decoration-none dropdown-toggle fw-medium d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?= !empty($_SESSION['avatar']) ? htmlspecialchars($_SESSION['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['username'] ?? 'U') . '&background=0D6EFD&color=fff&bold=true' ?>" alt="Avatar" class="rounded-circle" width="35" height="35">
                <span>Chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Bạn') ?></span>
              </a>
              
              <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 rounded-3">
                <li>
                    <a class="dropdown-item py-2 fw-medium" href="/LAPTRINHWEB/frontend/user/profile.php">
                        <i class="bi bi-person-circle me-2 text-secondary"></i> Hồ sơ cá nhân

                    </a>

                    <ul class="dropdown-menu dropdown-menu-end mt-2 shadow border-0" style="border-radius: 12px; min-width: 200px;">
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <li>
                                <a class="dropdown-item fw-bold py-2 text-primary" href="/LAPTRINHWEB/frontend/admin/dashboard.php">
                                    <i class="bi bi-speedometer2 me-2"></i>Trở về Admin
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                        <?php endif; ?>
                        
                        <li>
                            <a class="dropdown-item fw-medium py-2 text-dark" href="/LAPTRINHWEB/frontend/user/profile.php">
                                <i class="bi bi-person-vcard me-2 text-success"></i>Cập nhật thông tin
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        
                        <li>
                            <a class="dropdown-item text-danger fw-bold py-2" href="/LAPTRINHWEB/frontend/guest/logout.php" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất không?');">
                                <i class="bi bi-box-arrow-right me-2"></i>Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.dropdown-menu .dropdown-item { transition: all 0.2s ease-in-out; }
.dropdown-menu .dropdown-item:hover { background-color: #f8f9fa; transform: translateX(5px); }
</style>