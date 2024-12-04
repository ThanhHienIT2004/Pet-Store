<link rel="stylesheet" href="../asset/css/user.css">
<link rel="stylesheet" href="../asset/css/profile.css">


<div class="container-index-user-flex">
    <div class="cnt-col">
        <div class="sidebar">
            <div class="welcome">
                Pet Store, xin chaò!
            </div>
            <h2>TRANG TÀI KHOẢN</h2>
            <a href="index.php?page=index_user&pageuser=profile">Thông tin tài khoản</a>
            <a href="index.php?page=index_user&pageuser=orders">Đơn hàng của bạn</a>
            <a href="index.php?page=index_user&pageuser=change_password">Đổi mật khẩu</a>
            <a href="logout.php" class="logout">Đăng xuất</a>
        </div>
    </div>

    <div class="content-col">
        <?php
        // Lấy giá trị từ URL và làm sạch đầu vào
        $pageuser = isset($_GET['pageuser']) ? htmlspecialchars($_GET['pageuser']) : '';

        switch ($pageuser) {
            case 'orders':
                require_once 'orders.php';
                break;
            case 'change_password':
                require_once 'change_password.php';
                break;
            case 'orders_detail':
                require_once 'orders_detail.php';
                break;
            default:
                require_once 'profile.php';
                break;
        }
        ?>
    </div>

        <?php
        // Lấy giá trị từ URL và làm sạch đầu vào
        $pageuser = isset($_GET['pageuser']) ? htmlspecialchars($_GET['pageuser']) : '';

        switch ($pageuser) {
            case 'orders':
                require_once 'orders.php';
                break;
            case 'change_password':
                require_once 'change_password.php';
                break;
            case 'orders_detail':
                require_once 'orders_detail.php';
                break;
            default:
                require_once 'profile.php';
                break;
        }
        ?>
</div>
