<?php
// Xử lý yêu cầu đăng ký nếu có dữ liệu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        header('Content-Type: application/json');

        // Kết nối đến cơ sở dữ liệu
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "pet-store";

        $conn = new mysqli($servername, $username, $password, $dbname);

        // Kiểm tra kết nối
        if ($conn->connect_error) {
            die(json_encode(['success' => false, 'error' => 'Kết nối đến cơ sở dữ liệu thất bại.']));
        }

        // Lấy dữ liệu từ form
        $user = $conn->real_escape_string($_POST['username']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];

        // Kiểm tra mật khẩu xác nhận
        if ($password !== $confirmPassword) {
            echo json_encode([
                'success' => false, 
                'errors' => [
                    'confirmPassword' => 'Mật khẩu và xác nhận mật khẩu không khớp.'
                ]
            ]);
            $conn->close();
            exit();
        }

        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Kiểm tra tên người dùng đã tồn tại
        $queryUser = "SELECT * FROM users WHERE username = '$user'";
        $resultUser = $conn->query($queryUser);

        // Kiểm tra email đã tồn tại
        $queryEmail = "SELECT * FROM users WHERE email = '$email'";
        $resultEmail = $conn->query($queryEmail);

        if ($resultUser->num_rows > 0) {
            echo json_encode([
                'success' => false, 
                'errors' => [
                    'username' => 'Tên người dùng đã tồn tại.'
                ]
            ]);
        } elseif ($resultEmail->num_rows > 0) {
            echo json_encode([
                'success' => false, 
                'errors' => [
                    'email' => 'Email đã được sử dụng.'
                ]
            ]);
        } else {
            // Thêm người dùng mới vào cơ sở dữ liệu
            $query = "INSERT INTO users (username, email, pass) VALUES ('$user', '$email', '$hashedPassword')";

            if ($conn->query($query) === TRUE) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Đăng ký thất bại.']);
            }
        }

        $conn->close();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="../asset/css/register.css">
</head>
<body>
    <!-- Modal Form Đăng Ký -->
    <div id="registerModal" class="login-modal" style="display:none">
        <div class="login-modal-content">
            <span id="closeRegisterModalButton" class="close">&times;</span>
            <span id="backToLogin" class="back-arrow">&#8592; Quay lại</span> 
            <h2>Đăng Ký</h2>
            <form id="registerForm" method="post" class="login-form">
                <label for="register-username">Tên đăng nhập</label>
                <input type="text" id="register-username" name="username" required><br>
                <label for="register-email">Email</label><br>
                <input type="email" id="register-email" name="email" required><br>
                <label for="register-password">Mật khẩu</label><br>
                <input type="password" id="register-password" name="password" required><br>
                <label for="register-confirmPassword">Xác nhận mật khẩu</label><br>
                <input type="password" id="register-confirmPassword" name="confirmPassword" required><br>
                <div class="login-button-container">
                    <button type="submit" name="register" value="Đăng ký">Đăng ký</button>
                    <button type="reset">Xóa</button>
                </div>
                <div id="error-message-register" style="color: red;"></div>
            </form>
        </div>
    </div>
    <script src="../asset/js/register.js"></script>
</body>
</html>