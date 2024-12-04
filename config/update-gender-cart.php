<?php
require 'config.php';

// Gửi header JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['pet_id'] ?? '';
    $genderCart = $_POST['genderCart'] ?? '';

    error_log("Processing: pet_id=$itemId, genderCart=$genderCart");

    // Kiểm tra dữ liệu hợp lệ
    if (empty($itemId) || !in_array($genderCart, ['0', '1'])) {
        error_log("Invalid data");
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    try {
        // Cập nhật dữ liệu trong cơ sở dữ liệu
        $stmt = $conn->prepare("UPDATE cart_items SET genderCart = :genderCart WHERE pet_id = :pet_id");
        $result = $stmt->execute(['genderCart' => $genderCart, 'pet_id' => $itemId]);

        // Kiểm tra kết quả cập nhật
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cập nhật thất bại']);
        }

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()]);
    }
} else {
    error_log("Invalid request method");
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
