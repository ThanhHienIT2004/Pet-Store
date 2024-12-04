<?php
require '../../config/config.php'; // Kết nối cơ sở dữ liệu

// Xử lý yêu cầu xóa người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = $_POST['id'];

    try {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $message = "Người dùng đã được xóa.";
    } catch (PDOException $e) {
        $message = "Lỗi: " . $e->getMessage();
    }
}

// Xử lý yêu cầu cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];

    try {
        $sql = "UPDATE users SET fullname = :fullname, email = :email, phone = :phone, address = :address, dob = :dob WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $message = "Thông tin đã được cập nhật.";
    } catch (PDOException $e) {
        $message = "Lỗi: " . $e->getMessage();
    }
}

// Lấy danh sách người dùng từ cơ sở dữ liệu
try {
    $sql = "SELECT * FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Lỗi: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background-color: #f0f4f8;
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .admin-container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .message {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #3498db;
            color: #fff;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        button {
            background-color: #3498db;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }

        button:hover {
            background-color: #2980b9;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        form {
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        form button {
            background-color: #f1c40f;
        }

        form button:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Quản Lý Người Dùng</h2>

        <?php if (isset($message)) : ?>
            <div class="<?php echo strpos($message, 'Lỗi') !== false ? 'error' : 'message'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Ngày sinh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['address']); ?></td>
                        <td><?php echo htmlspecialchars($user['dob']); ?></td>
                        <td>
                            <!-- Form sửa thông tin người dùng -->
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="edit">Sửa</button>
                            </form>

                            <!-- Form xóa người dùng -->
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="delete" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Form thêm hoặc cập nhật người dùng -->
        <?php if (isset($_POST['edit'])) :
            $id = $_POST['id'];
            // Lấy thông tin người dùng để chỉnh sửa
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $userToEdit = $stmt->fetch();
        ?>
            <h3>Chỉnh sửa thông tin người dùng</h3>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($userToEdit['id']); ?>">
                <label for="fullname">Họ và tên:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($userToEdit['fullname']); ?>" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userToEdit['email']); ?>" required>
                <label for="phone">Điện thoại:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userToEdit['phone']); ?>" required>
                <label for="address">Địa chỉ:</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($userToEdit['address']); ?>" required>
                <label for="dob">Ngày sinh:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($userToEdit['dob']); ?>">
                <button type="submit" name="update">Cập nhật thông tin</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
