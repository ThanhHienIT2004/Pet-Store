<?php
require_once '../config/config.php'; // Đảm bảo rằng file này chứa thông tin kết nối database

// Xử lý hủy hoặc chuyển trạng thái đơn hàng thành "Đã xóa"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'])) {
        $order_id = $_POST['order_id'];

        // Kiểm tra trạng thái đơn hàng trước khi hủy
        $check_status_stmt = $conn->prepare("SELECT status FROM orders WHERE idOrder = ?");
        $check_status_stmt->execute([$order_id]);
        $order_status = $check_status_stmt->fetchColumn();

        if ($order_status === 'Đang xử lý') {
            try {
                $conn->beginTransaction();

                // Cập nhật trạng thái đơn hàng thành "Đã hủy"
                $update_order_stmt = $conn->prepare("UPDATE orders SET status = 'Đã hủy' WHERE idOrder = ?");
                $update_order_stmt->execute([$order_id]);

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
        $check_status_stmt = $conn->prepare("SELECT status FROM orders WHERE idOrder = ?");
        $check_status_stmt->execute([$delete_order_id]);
        $order_status = $check_status_stmt->fetchColumn();

        if ($order_status === 'Đã hủy' || $order_status === 'Đã giao') {
            try {
                $conn->beginTransaction();

                // Cập nhật trạng thái đơn hàng thành "Đã xóa"
                $update_order_stmt = $conn->prepare("UPDATE orders SET status = 'Đã xóa' WHERE idOrder = ?");
                $update_order_stmt->execute([$delete_order_id]);

                $conn->commit();

                header("Location: index.php?page=order_guest");
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

// Kiểm tra guest_id từ cookie
if (!isset($_COOKIE['guest_id'])) {
    die("Không tìm thấy thông tin đơn hàng cho khách.");
}

$guest_id = $_COOKIE['guest_id'];
$guest_email = $guest_id . '@guest.com';

try {
    // Tìm user_id của guest trong bảng users
    $user_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $user_stmt->execute([$guest_email]);
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("Không tìm thấy thông tin người dùng khách.");
    }
    
    $user_id = $user['id'];

    // Lấy thông tin đơn hàng, ẩn đơn hàng đã xóa
    $order_sql = "SELECT o.idOrder, o.orderDate, o.totalAmount, o.status, o.payment
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

<link rel="stylesheet" href="../asset/css/order_guest.css">

<h2 style="text-align: center; font-size: 30px; font-weight: bold; color: #333; margin-top: 20px;">ĐƠN HÀNG CỦA KHÁCH</h2>
<?php foreach ($orders as $order): ?>
    <div class='order-guest'>
        <div class="order-guest__main">
            <div class="order-guest__info-container">
                <h3 class="order-guest__title">Đơn hàng #<?= htmlspecialchars($order['idOrder']) ?></h3>
                <p class="order-guest__info"><span>Ngày đặt:</span> <span><?= htmlspecialchars($order['orderDate']) ?></span></p>
                <p class="order-guest__info"><span>Tổng tiền:</span> <span><?= number_format($order['totalAmount'], 0, ',', '.') ?> đ</span></p>
                <p class="order-guest__info"><span>Phương thức thanh toán:</span> <span><?= htmlspecialchars($order['payment']) ?></span></p>
                <p class="order-guest__info"><span>Trạng thái:</span> <span><?= htmlspecialchars($order['status']) ?></span></p>
                
                <!-- Hiển thị nút hủy nếu đơn hàng đang xử lý -->
                <?php if ($order['status'] === 'Đang xử lý'): ?>
                    <form class="order-guest__cancel-form" method="post" action="order_guest.php" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['idOrder']) ?>">
                        <button type="submit" class="order-guest__cancel-button">Hủy đơn hàng</button>
                    </form>
                
                <!-- Hiển thị nút xóa nếu đơn hàng đã hủy hoặc đã giao -->
                <?php elseif ($order['status'] === 'Đã hủy' || $order['status'] === 'Đã giao' || $order['status'] === 'Đã hoàn tiền'): ?>
                    <form class="order-guest__delete-form" method="post" action="order_guest.php" onsubmit="return confirm('Bạn có chắc chắn muốn chuyển đơn hàng này sang trạng thái Đã xóa?');">
                        <input type="hidden" name="delete_order_id" value="<?= htmlspecialchars($order['idOrder']) ?>">
                        <button type="submit" class="order-guest__delete-button">Xóa đơn hàng</button>
                    </form>
                <?php endif; ?>

            </div>
            <div class="order-guest__details">
                <h4 class="order-guest__subtitle">Chi tiết đơn hàng</h4>    
                <table class="order-guest__table">
                    <tr><th>Sản phẩm</th><th>Số lượng</th><th>Giá</th></tr>
                    <?php
                    if (isset($order_details[$order['idOrder']])) {
                        foreach ($order_details[$order['idOrder']] as $detail):
                    ?>
                        <tr>
                            <td class="order-guest__cell"><?= htmlspecialchars($detail['name']) ?></td>
                            <td class="order-guest__cell"><?= htmlspecialchars($detail['quantity']) ?></td>
                            <td class="order-guest__cell"><?= number_format($detail['price'], 0, ',', '.') ?> đ</td>
                        </tr>
                    <?php
                        endforeach;
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<?php if (empty($orders)): ?>
    <p>Không có đơn hàng nào.</p>
<?php endif; ?>
