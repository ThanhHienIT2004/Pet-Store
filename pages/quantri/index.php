<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập với tư cách admin chưa
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_username'])) {
    // Nếu chưa đăng nhập, chuyển hướng đến trang đăng nhập
    header("Location: login.php");
    exit();
}

// Kiểm tra xem người dùng có phải là admin không (giả sử admin có idgroup là 1)
if (!isset($_SESSION['admin_group']) || $_SESSION['admin_group'] != 1) {
    // Nếu không phải admin, đăng xuất và chuyển hướng đến trang đăng nhập
    session_unset();
    session_destroy();
    header("Location: login.php?error=unauthorized");
    exit();
}

// Tùy chọn: Kiểm tra thời gian không hoạt động
$inactive = 1800; // 30 phút
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    // Nếu quá thời gian không hoạt động, đăng xuất
    session_unset();
    session_destroy();
    header("Location: login.php?error=timeout");
    exit();
}
$_SESSION['last_activity'] = time(); // Cập nhật thời gian hoạt động

// Tùy chọn: Tái tạo ID phiên để ngăn chặn fixation attacks
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) { // Giảm xuống 30 giây để test
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Định nghĩa biến $page
$page = isset($_GET['page']) ? $_GET['page'] : '';

// Nếu mọi kiểm tra đều pass, admin có thể truy cập trang này
require 'functions.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản trị website</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Roboto', 'Arial', sans-serif;
    }

    html, body {
        height: 100%;
        width: 100%;
        background-color: #f5f7fa;
        color: #2c3e50;
    }

    .container {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
    }

    header {
        height: 80px;
        background-color: #3498db;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 10px rgba(52, 152, 219, 0.2);
    }

    header h1 {
        color: white;
        font-size: 2.2rem;
        font-weight: 500;
    }

    .noidung {
        display: flex;
        flex: 1;
        overflow: hidden;
    }

    aside {
        background-color: #2c3e50;
        padding: 25px;
        width: 240px;
        overflow-y: auto;
    }

    aside ul {
        list-style: none;
    }

    aside ul li {
        margin-bottom: 15px;
    }

    aside ul li a {
        color: #ecf0f1;
        text-decoration: none;
        font-size: 1.1rem;
        padding: 12px 15px;
        display: block;
        background-color: #34495e;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    aside ul li a:hover {
        background-color: #3498db;
        transform: translateX(5px);
    }

    main {
        flex: 1;
        background-color: white;
        padding: 30px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        overflow-y: auto;
    }

    main p {
        font-size: 1.1rem;
        color: #34495e;
        line-height: 1.6;
    }

    footer {
        text-align: center;
        font-size: 0.9rem;
        color: #7f8c8d;
        padding: 20px;
        background-color: #ecf0f1;
        border-top: 1px solid #bdc3c7;
    }

    footer a {
        color: #3498db;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    footer a:hover {
        color: #2980b9;
        text-decoration: underline;
    }
</style>

</head>

<body>
    <div class="container">
        <header>
            <h1>Quản trị Website</h1>
        </header>

        <div class="noidung">
            <aside>
                <ul>
                    <li><a href="index.php?page=pets_ds">Danh sách thú cưng</a></li>
                    <li><a href="index.php?page=pets_them">Thêm thú cưng</a></li>   
                    <li><a href="index.php?page=orders">Danh sách đơn hàng</a></li>
                    <li><a href="index.php?page=users">Quản lý người dùng</a></li>
                </ul>
            </aside>

            <main>
                <?php
                // Nhúng các trang con vào vùng nội dung chính dựa vào biến $page
                switch ($page) {
                    case "pets_ds":
                        require_once 'pets_ds.php';
                        break;
                    case "pets_them":
                        require_once 'pets_them.php';
                        break;
                    case "pets_sua":
                        require_once 'pets_sua.php';
                        break;
                    case "orders":
                        require_once 'orders.php';
                        break;
                    case "orders_detail":
                        require_once 'orders_detail.php';
                        break;
                    case "users":
                        require_once 'users.php';
                        break;
                    default:
                        echo "<p>Chào mừng đến với trang quản trị hệ thống thú cưng!</p>";
                        break;
                }
                ?>
            </main>
        </div>
    </div>
</body>

</html>
