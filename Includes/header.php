<?php
session_start();

// Kiểm tra trạng thái đăng nhập
$logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

?>
<link rel="stylesheet" href="../asset/css/index.css">
<link rel="stylesheet" href="../asset/css/banner.css">
<link rel="stylesheet" href="../asset/css/search.css">
<link rel="icon" type="image/x-icon" href="../asset/images/icon/logo.ico">

<nav>
    <ul class="nav-left">
        <li>
            <a href="../pages/index.php">
                Trang Chủ
            </a>
        </li>
        |
        <li class="dropdown">
            <a class="dropdown-btn">
                Thú Cưng 
            </a>
            <div class="dropdown-content">
                <a href="../pages/index.php?page=cat">
                    <img src="../asset/images/icon/cat-ico.png" alt="Cat Icon" style="vertical-align: middle;" />
                    Mèo
                </a>
                <a href="../pages/index.php?page=dog">
                    <img src="../asset/images/icon/dog-ico.png" alt="Dog Icon" style="vertical-align: middle;" />
                    Chó
                </a>
                <a href="../pages/index.php?page=parrot">
                    <img src="../asset/images/icon/parrot-ico.png" alt="Parrot Icon" style="vertical-align: middle;" />
                    Vẹt
                </a>
            </div>
        </li>
        |
        <li>
            <a href="../pages/index.php?page=about">
                Giới Thiệu
            </a>
        </li>
    </ul>
    
    <ul class="nav-center">
        <li class="search-container">
            <form name="formtim" action="../pages/index.php" method="get" class="search-form">
                <input type="hidden" name="page" value="search">
                <input name="tukhoa" id="tukhoa" type="text" placeholder="Tìm kiếm" />
                <input name="btntim" id="btntim" type="image" src="../asset/images/icon/search.png" alt="Search Button">
            </form>
        </li>
    </ul>
    
    <ul class="nav-right">
    <li class="nav-cart dropdown">
        <a class="text-cart dropdown-btn" href="../pages/index.php?page=cart">
            <div class="cart-icon-wrapper">
                <img src="../asset/images/icon/cart-ico.png" alt="Cart Icon" />
                <span class="cart-count"></span>
            </div>
            Giỏ hàng
        </a>  
    </li>

 
    <?php if ($logged_in): 
        require '../config/config.php'; // Ensure database connection
         
        // Check if the user is logged in
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: login.php'); // Redirect to login page if not logged in
            exit();
        }
        
        // Get the username from session
        $username = $_SESSION['username'];
        
        // Retrieve user information from the database
        try {
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$user) {
                echo "User does not exist.";
                exit();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            exit();
        }
        
        // Handle user information update
        
        


    ?>

    <!-- Người dùng đã đăng nhập -->
    <li class="dropdown">
        <a class="dropdown-btn">
            <img src="../asset/images/icon/user.png" alt="User Icon" />
            <?php echo htmlspecialchars($user['username']); ?>
        </a>
        <div class="dropdown-content">
            <a href="../pages/index.php?page=index_user&pageuser=orders">
                <img src="../asset/images/icon/order.png" style="vertical-align: middle;" />
                Lịch sử đặt hàng
            </a>
            <a href="../pages/index.php?page=index_user">
                <img src="../asset/images/icon/userprofile.png" style="vertical-align: middle;" />
                Thông tin
            </a>
            <a href="../pages/logout.php">
                <img src="../asset/images/icon/logout-ico.png" style="vertical-align: middle;" />
                Đăng xuất
            </a>
        </div>
    </li>
<?php else: ?>
    <!-- Người dùng chưa đăng nhập -->
    <li class="dropdown">
        <a class="dropdown-btn">
            <img src="../asset/images/icon/user.png" alt="User Icon" />
            Tài khoản
        </a>
        <div class="dropdown-content">
        <?php if (!$logged_in): ?>
            <a href="../pages/index.php?page=order_guest">
                <img src="../asset/images/icon/order.png" style="vertical-align: middle;" />
                Lịch sử đặt hàng
            </a>
        <?php endif; ?>
        
            <a href="" onclick="openLoginModal(); return false;">
                <img class="circle-button" src="../asset/images/icon/login-ico.png" alt="Login" style="vertical-align: middle;">
                Đăng nhập
            </a>
            <a href="" onclick="openRegisterModal(); return false;">
                <img class="circle-button" src="../asset/images/icon/register-ico.png" alt="Register" style="vertical-align: middle;">
                Đăng ký
            </a>
        </div>
    </li>
<?php endif; ?>
