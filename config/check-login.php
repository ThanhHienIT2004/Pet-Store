<?php
session_start();

// Khai báo các thông số kết nối cơ sở dữ liệu
require 'config.php'; // Đảm bảo config.php có kết nối CSDL

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Vui lòng nhập đầy đủ thông tin.']);
        exit;
    }

    try {
        // Kiểm tra thông tin đăng nhập
        $sql = "SELECT id, username, pass FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['pass'])) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;

            echo json_encode([
                'success' => true,
                'message' => "Chào mừng, " . htmlspecialchars($user['username']) . "!"
            ]);
        } else {
            // Đăng nhập thất bại
            echo json_encode([
                'success' => false,
                'error' => 'Tên đăng nhập hoặc mật khẩu không đúng.'
            ]);
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Lỗi khi truy vấn cơ sở dữ liệu.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Phương thức không được hỗ trợ.']);
}
?>
