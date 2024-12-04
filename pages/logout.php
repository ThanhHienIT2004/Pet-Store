<?php
session_start();
session_destroy();
header("Location: ../pages/index.php"); // Chuyển hướng về trang chủ sau khi đăng xuất
exit();
?>