<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/AIStudyHub/User/frontend/documents/home.php">AIStudyHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="/AIStudyHub/User/frontend/documents/documents.php">Tài liệu</a></li>
        <li class="nav-item"><a class="nav-link" href="/AIStudyHub/User/frontend/chatbot/chats.php">Chat với AI</a></li>
      </ul>
      <div class="navbar-nav">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link text-white" href="/AIStudyHub/User/frontend/profile/profile.php">Chào, <?php echo htmlspecialchars($_SESSION['username']); ?></a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link text-warning" href="/AIStudyHub/Admin/frontend/dashboard/dashboard.php">Quản trị</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="btn btn-sm btn-danger ms-2" href="/AIStudyHub/logout.php">Đăng xuất</a></li>
        <?php else: ?>
            <a class="btn btn-outline-light me-2" href="/AIStudyHub/Guest/frontend/auth/login.php">Đăng nhập</a>
            <a class="btn btn-primary" href="/AIStudyHub/Guest/frontend/auth/register.php">Đăng ký</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>