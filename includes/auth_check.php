<?php
// Centralized auth checks for the project
if (session_status() === PHP_SESSION_NONE) session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /LAPTRINHWEB/frontend/guest/login.php');
        exit();
    }
}

function require_admin() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: /LAPTRINHWEB/frontend/guest/login.php');
        exit();
    }
}

?>
