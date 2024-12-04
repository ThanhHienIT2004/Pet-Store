<?php
// Kết nối cơ sở dữ liệu
require_once 'functions.php';

// Hàm lấy chi tiết đơn hàng
function layChiTietDonHang($order_id) {
    global $conn;
    // Truy vấn để lấy chi tiết đơn hàng
    $sql = "SELECT o.idOrder AS order_id, o.orderDate, o.status AS order_status, 
                   p.name AS pet_name, od.quantity, od.price, (od.quantity * od.price) AS item_total, od.genderOrder
            FROM orders o
            JOIN order_details od ON o.idOrder = od.order_id
            JOIN pets p ON od.pet_id = p.id
            WHERE o.idOrder = ?
            ORDER BY p.name";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy mã đơn hàng từ URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Lấy chi tiết đơn hàng
$details = layChiTietDonHang($order_id);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f0f4f8;
            color: #333;
            line-height: 1.6;
        }

        h4 {
            text-align: center;
            margin: 30px 0;
            padding: 15px;
            font-size: 28px;
            color: #2c3e50;
            background-color: #ecf0f1;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            margin: 20px 0;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .table th {
            background-color: #3498db;
            color: #fff;
            font-weight: 600;
            text-transform: uppercase;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover {
            background-color: #f5f7fa;
        }

        .table td img {
            width: 80px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .btn {
            padding: 12px 24px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: inline-block;
            margin: 20px 5px 0;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-back {
            background-color: #3498db;
        }

        .btn-back:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <h4>Chi Tiết Đơn Hàng #<?= $order_id ?></h4>
    <?php if (!empty($details)) { ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giới tính</th>
                    <th>Số lượng</th>
                    <th>Đơn giá</th>
                    <th>Tổng giá</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_order_price = 0;
                foreach ($details as $detail) {
                    $total_order_price += $detail['item_total'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($detail['pet_name']) ?></td>
                        <td><?= isset($detail['genderOrder']) ? ($detail['genderOrder'] == 1 ? 'Đực' : 'Cái') : 'Không xác định' ?></td>
                        <td><?= $detail['quantity'] ?></td>
                        <td><?= number_format($detail['price'], 0, ',', '.') ?> VND</td>
                        <td><?= number_format($detail['item_total'], 0, ',', '.') ?> VND</td>
                    </tr>
                <?php } ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="3" style="text-align: right; font-weight: bold;">Tổng cộng</td>
                    <td><?= number_format($total_order_price, 0, ',', '.') ?> VND</td>
                </tr>
            </tbody>
        </table>
    <?php } else { ?>
        <p>Không có thông tin chi tiết cho đơn hàng này.</p>
    <?php } ?>
    <div style="text-align: center; margin: 20px;">
        <a href="index.php?page=orders" class="btn btn-back">Trở về</a>
    </div>
</body>
</html>
