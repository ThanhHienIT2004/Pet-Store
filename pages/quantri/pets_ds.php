<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANH SÁCH THÚ CƯNG</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f0f4f8;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        h4 {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            font-size: 26px;
            color: #2c3e50;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background-color: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table th, .table td {
            padding: 15px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #e0e0e0;
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

        .table tr:hover {
            background-color: #f5f5f5;
            transition: background-color 0.3s ease;
        }

        .table td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .table td.description {
        max-width: 200px; /* Điều chỉnh chiều rộng tối đa theo nhu cầu */
        max-height: 50px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Thêm tooltip khi hover */
    .table td.description:hover {
        position: relative;
    }

    .table td.description:hover::after {
        content: attr(data-full-text);
        position: absolute;
        left: 0;
        top: 100%;
        z-index: 1;
        background: #fff;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        white-space: normal;
        max-width: 300px; /* Điều chỉnh chiều rộng tối đa của tooltip */
        word-wrap: break-word;
    }

        .btn {
            padding: 8px 15px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 13px;
            display: inline-block;
            margin: 0 5px;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            font-weight: 600;
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
                margin-bottom: 15px;
                border-radius: 5px;
                overflow: hidden;
            }
            
            .table td {
                border: none;
                position: relative;
                padding-left: 50%;
            }
            
            .table td:before {
                content: attr(data-label);
                position: absolute;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>
    <h4>DANH SÁCH THÚ CƯNG</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Tên Thú Cưng</th>
                <th>Giá</th>
                <th>Giá Khuyến Mãi</th>
                <th>Giới Tính</th>
                <th>Số Lượng</th>
                <th>Hình Ảnh</th>
                <th>Danh Mục</th>
                <th>Hot</th>
                <th>Mô Tả</th> <!-- Thêm cột Mô Tả -->
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php
            require_once 'functions.php';
            $listPets = layDanhSachPets(); // Gọi hàm lấy danh sách thú cưng
            foreach ($listPets as $row) {
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= number_format($row['price'], 0, ',', '.') ?> VND</td>
                <td><?= $row['priceSale'] ? number_format($row['priceSale'], 0, ',', '.') . ' VND' : 'Không có' ?></td>
                <td><?= $row['gender'] == 1 ? 'Đực' : 'Cái' ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><img src="<?php echo '../' . htmlspecialchars($row['urlImg']); ?>" alt="<?= $row['name'] ?>"></td>
                <td><?= $row['idLoai'] ?></td>
                <td><?= $row['hot'] == 1 ? 'Hot' : 'Không' ?></td>
                <td class="description" data-full-text="<?= htmlspecialchars($row['description']) ?>">
                    <?= htmlspecialchars($row['description']) ?>
                </td> <!-- Hiển thị mô tả -->
                <td>
                    <a href="pets_sua.php?id=<?= $row['id'] ?>" class="btn btn-edit">Sửa</a>
                    <a href="pets_xoa.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</body>
</html>
