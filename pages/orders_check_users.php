<?php
require_once '../config/config.php'; // Đảm bảo rằng file này chứa thông tin kết nối database
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION['user_id'])) {
    die("Bạn phải đăng nhập để xem đơn hàng.");
}

$user_id = $_SESSION['user_id'];

// Xử lý hủy hoặc chuyển trạng thái đơn hàng thành "Đã xóa"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];

        // Kiểm tra trạng thái đơn hàng trước khi hủy
        $check_status_stmt = $conn->prepare("SELECT status FROM orders WHERE idOrder = ? AND user_id = ?");
        $check_status_stmt->execute([$order_id, $user_id]);
        $order_status = $check_status_stmt->fetchColumn();

        if ($order_status === 'Đang xử lý') {
            try {
                $conn->beginTransaction();

                // Cập nhật trạng thái đơn hàng thành "Đã hủy"
                $update_order_stmt = $conn->prepare("UPDATE orders SET status = 'Đã hủy' WHERE idOrder = ? AND user_id = ?");
                $update_order_stmt->execute([$order_id, $user_id]);

                $conn->commit();

                header("Location: index.php?page=order_guest");
                exit();
            } catch (Exception $e) {
                $conn->rollBack();
                $error_message = "Lỗi khi hủy đơn hàng: " . $e->getMessage();
            }
        } else {
            $error_message = "Chỉ có thể hủy đơn hàng khi đang ở trạng thái 'Đang xử lý'.";
        }
    } elseif (isset($_POST['delete_order_id'])) {
        $delete_order_id = $_POST['delete_order_id'];

        // Kiểm tra trạng thái đơn hàng trước khi xóa
        $check_status_stmt = $conn->prepare("SELECT status FROM orders WHERE idOrder = ? AND user_id = ?");
        $check_status_stmt->execute([$delete_order_id, $user_id]);
        $order_status = $check_status_stmt->fetchColumn();

        if ($order_status === 'Đã hủy' || $order_status === 'Đã giao') {
            try {
                $conn->beginTransaction();

                // Cập nhật trạng thái đơn hàng thành "Đã xóa"
                $update_order_stmt = $conn->prepare("UPDATE orders SET status = 'Đã xóa' WHERE idOrder = ? AND user_id = ?");
                $update_order_stmt->execute([$delete_order_id, $user_id]);

                $conn->commit();

                header("Location: index.php?page=index_user&pageuser=orders");
                exit();
            } catch (Exception $e) {
                $conn->rollBack();
                $error_message = "Lỗi khi xóa đơn hàng: " . $e->getMessage();
            }
        } else {
            $error_message = "Chỉ có thể xóa đơn hàng khi đã hoàn thành hoặc đã hủy.";
        }
    }
}

try {
    // Lấy thông tin đơn hàng, ẩn đơn hàng đã xóa
    $order_sql = "SELECT o.idOrder, o.orderDate, o.totalAmount, o.status 
                  FROM orders o 
                  WHERE o.user_id = ? AND o.status != 'Đã xóa'
                  ORDER BY o.orderDate DESC";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([$user_id]);
    $orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Truy vấn chi tiết đơn hàng cho tất cả các đơn hàng
    $detail_sql = "SELECT od.order_id, od.pet_id, p.name, od.quantity, od.price 
                   FROM order_details od 
                   JOIN pets p ON od.pet_id = p.id 
                   WHERE od.order_id IN (SELECT idOrder FROM orders WHERE user_id = ?)";
    $detail_stmt = $conn->prepare($detail_sql);
    $detail_stmt->execute([$user_id]);
    $all_details = $detail_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tổ chức chi tiết đơn hàng theo idOrder
    $order_details = [];
    foreach ($all_details as $detail) {
        $order_details[$detail['order_id']][] = $detail;
    }

} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}
?>
