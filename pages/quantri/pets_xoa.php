<?php
require_once 'functions.php';

$id = $_GET['id'];
settype($id, "string"); // Để phù hợp với kiểu dữ liệu varchar của id trong bảng pets

// Gọi hàm xóa thú cưng
xoaPets($id);

// Chuyển về trang danh sách thú cưng
header("location: index.php?page=pets_ds");
exit();
?>
