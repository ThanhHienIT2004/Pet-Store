<?php
require_once 'functions.php';

// Lấy id của đơn hàng cần xóa từ URL
$id = $_GET['order_id'] ?? '';
$id = intval($id);

// Xóa đơn hàng và chi tiết của nó
if ($id) {
    $kq = xoaDonHang($id); // Bạn cần tạo hàm `xoaDonHang` trong file `functions.php`

    if ($kq) {
        header("location: index.php?page=orders");
        exit();
    } else {
        echo "Không thể xóa đơn hàng.";
    }
} else {
    echo "ID đơn hàng không hợp lệ.";
}
?>
