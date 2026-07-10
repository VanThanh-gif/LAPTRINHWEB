<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm py-3">
  <div class="container">
    
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="/LAPTRINHWEB/frontend/user/document.php">
      <i class="bi bi-layers-fill text-primary fs-4"></i>
      <span>AI<span class="text-primary">StudyHub</span></span>
    </a>
    
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 fw-medium">
        <li class="nav-item">
            <a class="nav-link" href="/LAPTRINHWEB/frontend/user/document.php">
                <i class="bi bi-folder2-open me-1"></i> Kho Tài liệu
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="/LAPTRINHWEB/frontend/services/chats.php">
                <i class="bi bi-robot me-1"></i> Chatbot AI
            </a>
        </li>
      </ul>
      
      <div class="d-flex align-items-center gap-3">
        <?php if (isset($_SESSION['user_id'])): ?>
            
            <div class="dropdown">
              <a class="text-white text-decoration-none dropdown-toggle fw-medium d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'U') ?>&background=0D6EFD&color=fff&bold=true" alt="Avatar" class="rounded-circle" width="35" height="35">
                <span>Chào, <?= htmlspecialchars($_SESSION['username'] ?? 'Bạn') ?></span>
              </a>
              
              <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 rounded-3">
                <li>
                    <a class="dropdown-item py-2 fw-medium" href="/LAPTRINHWEB/frontend/user/profile.php">
                        <i class="bi bi-person-circle me-2 text-secondary"></i> Hồ sơ cá nhân
                    </a>
                </li>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li>
                        <a class="dropdown-item py-2 fw-medium" href="/LAPTRINHWEB/frontend/admin/dashboard.php">
                            <i class="bi bi-speedometer2 me-2 text-warning"></i> Quản trị hệ thống
                        </a>
                    </li>
                <?php endif; ?>
                
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item py-2 fw-medium text-danger" href="/LAPTRINHWEB/backend/auth/logout.php">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </a>
                </li>
              </ul>
            </div>
            
        <?php else: ?>
            <a class="btn btn-outline-light px-4 rounded-pill fw-medium transition-all" href="/LAPTRINHWEB/frontend/guest/login.php">Đăng nhập</a>
            <a class="btn btn-primary px-4 rounded-pill fw-medium shadow-sm transition-all" href="/LAPTRINHWEB/frontend/guest/register.php">Đăng ký</a>
        <?php endif; ?>
      </div>
      
    </div>
  </div>
</nav>