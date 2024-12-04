<?php

require 'config.php';

session_start();

header('Content-Type: application/json');

// Kiểm tra kết nối có tồn tại hay không
if (!$conn) {
    echo json_encode(['error' => "Kết nối cơ sở dữ liệu thất bại."]);
    exit;
}

function getTotalQuantity() {
    if (isset($_SESSION['user_id'])) {
        // Người dùng đã đăng nhập
        global $conn;
        try {
            $stmt = $conn->prepare("
                SELECT SUM(cart_items.quantity) as totalQuantity
                FROM cart
                JOIN cart_items ON cart.cart_id = cart_items.cart_id
                WHERE cart.user_id = :user_id
            ");
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($result['totalQuantity'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error counting cart items: " . $e->getMessage());
            return 0;
        }
    } elseif (isset($_COOKIE['guest_cart'])) {
        // Khách (guest)
        $guestCart = json_decode(gzuncompress(base64_decode($_COOKIE['guest_cart'])), true);
        return (int)array_sum(array_column($guestCart, 'quantity'));
    }
    return 0;
}

try {
    $totalQuantity = getTotalQuantity();
    echo json_encode(['totalQuantity' => $totalQuantity]);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => "Có lỗi xảy ra khi kiểm tra giỏ hàng."]);
}
exit;