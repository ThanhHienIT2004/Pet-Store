<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANH SÁCH ĐƠN HÀNG</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f0f4f8;
            color: #333;
            font-size: 15px;
        }

        h4 {
            text-align: center;
            margin: 20px 0;
            padding: 12px;
            font-size: 24px;
            color: #2c3e50;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin: 20px 0;
            overflow: hidden;
        }

        .table th, .table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
            transition: background-color 0.3s ease;
        }

        .table th {
            background-color: #3498db;
            color: #ffffff;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background-color: #f5f5f5;
        }

        .btn {
            padding: 7px 14px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            display: inline-block;
            margin: 0 3px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            font-weight: 600;
        }

        .btn-view {
            background-color: #3498db;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }

        .btn-edit {
            background-color: #f1c40f;
        }

        .btn-edit:hover {
            background-color: #f39c12;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        @media (max-width: 768px) {
            .table, .table thead, .table tbody, .table th, .table td, .table tr {
                display: block;
            }
            
            .table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            
            .table tr {
                border: 1px solid #ccc;
                margin-bottom: 10px;
            }
            
            .table td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
                font-size: 14px;
            }
            
            .table td:before {
                position: absolute;
                top: 6px;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                content: attr(data-label);
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
    <h4>DANH SÁCH ĐƠN HÀNG</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Mã Đơn Hàng</th>
                <th>Ngày Đặt</th>
                <th>Tổng Tiền</th>
                <th>Phương Thức Thanh Toán</th>
                <th>Trạng Thái</th>
                <th>Người Nhận</th>
                <th>SĐT</th>
                <th>Địa Chỉ</th>
                <th>Chi Tiết</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once 'functions.php';
            $listOrders = layDanhSachDonHang(); // Gọi hàm lấy danh sách đơn hàng
            foreach ($listOrders as $row) {
            ?>
            <tr>
                <td><?= htmlspecialchars($row['order_id']) ?></td>
                <td><?= htmlspecialchars($row['orderDate']) ?></td>
                <td><?= number_format($row['total_price'], 0, ',', '.') ?> VND</td>
                <td><?= htmlspecialchars($row['payment']) ?></td>
                <td style = "color:red;"><?= htmlspecialchars($row['order_status']) ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= htmlspecialchars($row['phone']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td><a href="orders_detail.php?order_id=<?= $row['order_id'] ?>" class="btn btn-view">Xem Chi Tiết</a></td>
                <td>
                    <a href="orders_sua.php?order_id=<?= $row['order_id'] ?>" class="btn btn-edit">Sửa</a>
                    <a href="orders_xoa.php?order_id=<?= $row['order_id'] ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
