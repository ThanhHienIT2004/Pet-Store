    <?php
    require 'functions.php'; // Ensure database connection

    // Get the order ID from the URL
    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : null;
    if (!$order_id) {
        echo "Order ID is required.";
        exit();
    }

    // Retrieve order information from the database
    try {
        $sql = "SELECT * FROM orders WHERE idOrder = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch();

        if (!$order) {
            echo "Order does not exist.";
            exit();
        }

        // Retrieve order details
        $sql = "SELECT * FROM order_details WHERE order_id = :order_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order_details = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }

    // Handle order update
    $message = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $totalAmount = $_POST['totalAmount'];
        $status = $_POST['status'];

        try {
            $sql = "UPDATE orders SET totalAmount = :totalAmount, status = :status WHERE idOrder = :order_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':totalAmount', $totalAmount);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->execute();

            $message = "Cập nhật đơn hàng thành công";
            header("Location: index.php?page=orders&order_id=$order_id");
            exit();
        } catch (PDOException $e) {
            $message = "Error updating order: " . $e->getMessage();
        }
    }   
    ?>

<body>
    <div class="container">
        <div class="tt">
            <div class="content">
                <h2>Thông tin đơn hàng</h2>
                <form id="orderForm" class="order" action="" method="post">
                    <div class="form-group">
                        <label for="order_id">Mã đơn hàng:</label>
                        <input type="text" id="order_id" name="order_id" value="<?php echo htmlspecialchars($order['idOrder']); ?>" readonly class="input-disabled">
                    </div>

                    <div class="form-group" style="display: none;">
                        <label for="totalAmount">Tổng tiền:</label>
                        <input type="text" id="totalAmount" name="totalAmount" value="<?php echo htmlspecialchars($order['totalAmount']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Trạng thái:</label>
                        <select id="status" name="status" required class="input-select">
                            <option value="Đang xử lý" <?php echo $order['status'] == 'Đang xử lý' ? 'selected' : ''; ?>>Đang xử lý</option>
                            <option value="Đã hủy" <?php echo $order['status'] == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                            <option value="Đang vận chuyển" <?php echo $order['status'] == 'Đang vận chuyển' ? 'selected' : ''; ?>>Đang vận chuyển</option>
                            <option value="Đã giao" <?php echo $order['status'] == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                            <option value="Đã hoàn tiền" <?php echo $order['status'] == 'Đã hoàn tiền' ? 'selected' : ''; ?>>Đã hoàn tiền</option>
                            <option value="Đã xóa" <?php echo $order['status'] == 'Đã xóa' ? 'selected' : ''; ?>>Đã xóa</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-left: 30%;">Cập nhật đơn hàng</button>

                    <button class="btn-back">
                        <a href="index.php?page=orders">Trở về</a>
                        <?php if ($message) { echo '<p class="error-message">' . htmlspecialchars($message) . '</p>'; } ?>
                    </button>
                </form>

                <h3>Chi tiết đơn hàng</h3>
                <table class="order-details">
                    <thead>
                        <tr>
                            <th>Mã thú cưng</th>
                            <th>Giá</th>
                            <th>Số lượng</th>                       
                            <th>Tổng tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    $totalQuantity = 0; // Biến để tính tổng số lượng
                    $totalAmount = 0; // Biến để tính tổng tiền

                    foreach ($order_details as $detail): 
                        $itemTotal = $detail['price'] * $detail['quantity'];
                        $totalQuantity += $detail['quantity'];
                        $totalAmount += $itemTotal;
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detail['pet_id']); ?></td>
                            <td><?php echo number_format($detail['price'], 0, '.', '.'); ?> VND</td>
                            <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
                            <td><?php echo number_format($itemTotal, 0, '.', '.'); ?> VND</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2"><strong>Tổng cộng:</strong></td>
                            <td><strong><?php echo number_format($totalQuantity, 0, '.', '.'); ?></strong></td>
                            <td><strong><?php echo number_format($totalAmount, 0, '.', '.'); ?> VND</strong></td>
                        </tr>
                    </tfoot>
                </table>
                
            </div>
        </div>
    </div>
</body>



<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        color: #333;
        line-height: 1.6;
    }

    .container {
        max-width: 800px;
        margin: 20px auto;
        padding: 30px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
        display: block;
        margin-bottom: 8px;
        color: #34495e;
    }

    .input-disabled, .input-text, .input-select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 16px;
    }

    .input-disabled {
        background-color: #f9f9f9;
    }

    .error-message {
        color: #e74c3c;
        font-size: 14px;
        margin-top: 10px;
        padding: 10px;
        background-color: #fadbd8;
        border-radius: 5px;
        text-align: center;
    }

    table.order-details {
        width: 100%;
        border-collapse: collapse;
        margin-top: 30px;
    }

    table.order-details th, table.order-details td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    table.order-details th {
        background-color: #3498db;
        color: white;
    }

    .btn {
        padding: 12px 24px;
        text-decoration: none;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        display: inline-block;
        margin: 10px 5px 0;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #3498db;
    }

    .btn-secondary {
        background-color: #95a5a6;
    }

    .btn-back {
        background-color: #34495e;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: inline-block;
        text-decoration: none;
        margin-top: 10px;
    }

    .btn-back:hover {
        background-color: #2c3e50;
    }

    .btn-back a {
        color: white;
        text-decoration: none;
    }

    .btn:hover {
        opacity: 0.9;
    }

    h3 {
        color: #2c3e50;
        margin-top: 30px;
        margin-bottom: 20px;
        text-align: center;
    }

    .order-details {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .order-details thead {
        background-color: #3498db;
        color: white;
    }

    .order-details th,
    .order-details td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    .order-details tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .order-details tbody tr:hover {
        background-color: #f1f3f5;
    }

    .order-details tfoot {
        font-weight: bold;
        background-color: #ecf0f1;
    }

    .order-details tfoot td {
        border-top: 2px solid #3498db;
    }

    @media screen and (max-width: 600px) {
        .order-details {
            font-size: 14px;
        }

        .order-details th,
        .order-details td {
            padding: 8px 10px;
        }
    }
</style>