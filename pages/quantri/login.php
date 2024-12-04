<?php
session_start();

if (isset($_POST['loginAdmin'])) {
    // Tiếp nhận user và pass từ form
    $username = trim(strip_tags($_POST['username']));
    $password = trim(strip_tags($_POST['password']));

    // Kết nối cơ sở dữ liệu
    require_once("functions.php");

    // Truy vấn kiểm tra username tồn tại
    $sql = "SELECT id, username, pass, idgroup FROM users WHERE username = :username AND idgroup = 1"; // idgroup 1 cho admin
    $stmt = $conn->prepare($sql);
    $stmt->execute([':username' => $username]);

    if ($stmt->rowCount() == 0) {
        $_SESSION['error'] = "Tài khoản admin không tồn tại";
    } else {
        // Lấy thông tin người dùng
        $user = $stmt->fetch();

        // Kiểm tra mật khẩu
        if (password_verify($password, $user['pass'])) {
            // Đăng nhập thành công
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_group'] = $user['idgroup'];

            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Mật khẩu không đúng";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet" href="../../asset/css/login.css">
</head>
<body>
    <!-- Modal Form Đăng Nhập -->
    <div id="loginModal" class="login-modal">
        <div class="login-modal-content">
            <h2>Đăng Nhập Admin</h2>

            <form id="loginForm" class="login-form" action="" method="POST">
                <div class="form-group-item" style="padding-bottom: 10px">
                    <label for="login-username">Tên đăng nhập:</label>
                    <input type="text" id="login-username" name="username" required>
                </div>
                <div class="form-group-item" style="width: 98%">
                    <label for="login-password">Mật khẩu:</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <hr>
                <div class="login-button-container" style="width: 70%">
                    <input type="submit" name="loginAdmin" value="Đăng Nhập">
                    <button type="reset">Xóa</button>
                </div>
                <div id="error-message" class="login-error-message"></div>
            </form>
        </div>
    </div>
</body>
</html>

