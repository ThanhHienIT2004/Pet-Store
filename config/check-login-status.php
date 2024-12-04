<?php
session_start();

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo "Chào mừng, " . htmlspecialchars($_SESSION['username']) . "!";
} else {
    echo "Bạn chưa đăng nhập. Vui lòng đăng nhập.";
    // Hoặc chuyển hướng tới trang đăng nhập
    header('Location: login.php');
    exit();
}
?>
