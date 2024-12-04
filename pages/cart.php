<?php
require '../config/config.php';

// Kiểm tra trạng thái đăng nhập
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
    // Người dùng đã đăng nhập, sử dụng user_id từ session
    $user_id = $_SESSION['user_id'];
    $is_logged_in = true;

    // lấy thông tin user
    $user_info = getUserInfo($user_id);

} else {
    // Người dùng chưa đăng nhập
    $is_logged_in = false;
}

// Kiểm tra kết nối có tồn tại hay không
if (!$conn) {
    die("Kết nối cơ sở dữ liệu thất bại.");
}

try {
    if ($is_logged_in) {
        // Truy vấn các mặt hàng trong giỏ hàng của người dùng từ database
        $stmt = $conn->prepare("
            SELECT pets.id, pets.name, pets.price, pets.priceSale, pets.urlImg, cart_items.quantity, cart_items.price as item_price, cart_items.genderCart
            FROM cart
            JOIN cart_items ON cart.cart_id = cart_items.cart_id
            JOIN pets ON cart_items.pet_id = pets.id 
            WHERE cart.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $user_id]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($cartItems === false) {
            throw new Exception("Không thể truy xuất giỏ hàng.");
        }
    } else {
        // Lấy giỏ hàng từ cookie
        $cartItems = getGuestCart();
    }

    // Tính tổng số tiền
    $totalAmount = 0;
    if (!empty($cartItems)) {
        foreach ($cartItems as $item) {
            $price = $is_logged_in ? $item['item_price'] : $item['price'];
            $totalAmount += $price * $item['quantity'];
        }
    }
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
    exit;
}

// Hàm lấy giỏ hàng từ cookie
function getGuestCart() {
    if (isset($_COOKIE['guest_cart'])) {
        return json_decode(gzuncompress(base64_decode($_COOKIE['guest_cart'])), true);
    }
    return [];
}

// Hàm lấy thông tin user
function getUserInfo($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT fullname, address, phone FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<link rel="stylesheet" href="../asset/css/cart.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../asset/css/admin.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="../asset/css/payment.css?v=<?php echo time(); ?>">

<div class="cart-flex">
    <!-- -----------bảng invoice -------------->
    <div class="invoice-flex">
        <?php if (!empty($cartItems)): ?>
        <div class="title-invoice-flex">
            <input type="checkbox" class="checkbox-all-btn-cart" onclick="toggleSelectAll(this)">
            <b class="title-invoice">Giỏ hàng của bạn</b>
        </div>

        <div class="container-invoice-list">
            <?php foreach ($cartItems as $item): ?>
            <div class="invoice-item" data-id="<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>" data-gender="<?php echo htmlspecialchars($item['genderCart'] ?? ''); ?>">
                <input type="checkbox" class="checkbox-btn-cart" data-id="<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>"
                    onclick="selectItem('<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>')">

                <div class="image-container">
                    <img class="imgInvoice" src="<?php echo htmlspecialchars($item['urlImg']); ?>"
                        alt="<?php echo htmlspecialchars($item['name']); ?>">
                </div>

                <div class="invoice-text">
                    <p class="name-pet-cart"><?php echo htmlspecialchars($item['name']); ?></p>
                    <p>Giá: <?php echo number_format($is_logged_in ? ($item['priceSale'] ?? $item['price']) : $item['price'], 0, ',', '.'); ?>đ</p>
                    <p class="totalPrice" data-id="<?php echo $item['pet_id'] ?? $item['id']; ?>">Tổng giá:
                        <?php echo number_format(($is_logged_in ? ($item['priceSale'] ?? $item['price']) : $item['price']) * $item['quantity'], 0, ',', '.'); ?>đ</p>
                </div>

                <div class="btn-container">
                    <div class="gender-pet">
                        <input type="radio" name="gender-<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>" value="1" <?php echo ($item['genderCart'] == '1') ? 'checked' : ''; ?>> Đực
                        <input type="radio" name="gender-<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>" value="0" <?php echo ($item['genderCart'] == '0') ? 'checked' : ''; ?>> Cái
                    </div>

                    <div class="count">
                        <button class="quantity-btn minus" data-id="<?php echo $item['pet_id'] ?? $item['id']; ?>">-</button>
                        <span id="quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                        <button class="quantity-btn plus" data-id="<?php echo $item['pet_id'] ?? $item['id']; ?>">+</button>
                    </div>
                </div>

                <button class="btn-cancel-pet"
                    onclick="removeFromCart('<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>')">Hủy</button>
                <button class="btn-order-pet" onclick="showOrderForm('<?php echo htmlspecialchars($item['pet_id'] ?? $item['id']); ?>')">Đặt hàng</button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
            <p style="font-size: 7vb">Không có sản phẩm nào trong giỏ hàng!</p>
            <p style="font-size: 5vb">Hãy chọn một mục thú cưng bạn quan tâm</p>
            <div class="cnt-buy">
                <button class="btn-pets-buy"><a href="../pages/index.php?page=cat">
                    <img src="../asset/images/icon/cat-ico.png" alt="Cat Icon" />
                    Mèo
                </a></button>
                
                <button class="btn-pets-buy"><a href="../pages/index.php?page=dog">
                    <img src="../asset/images/icon/dog-ico.png" alt="Dog Icon" />
                    Chó
                </a></button>

                <button class="btn-pets-buy"><a href="../pages/index.php?page=parrot">
                    <img src="../asset/images/icon/parrot-ico.png" alt="Parrot Icon"/>
                    Vẹt
                </a></button>
            </div>
        <?php endif; ?>
    </div>

    <!-- -------------------check-list----------  -->
    <div class="invoice-check-flex">
        <div class="invoice-check">
            <!-- khung sản phẩm đã chọn -->
            <div class="container-invoice-check"></div>
            <!-- nút đặt tất cả -->
            <div class="order-summary">
                <p>Tổng số tiền tất cả sản phẩm:
                    <span class="total-amount">
                        <?php echo number_format($totalAmount, 0, ',', '.'); ?>đ
                    </span>
                </p>
                <button class="btn-order-all" onclick="showOrderAllForm()">Đặt hàng tất cả</button>
            </div>
        </div>
    </div>

    <!-- ---------------------------form đặt hàng---------------------- -->
    <div class="cart-form" id="orderForm" style="display: none;">
        <!-- Thêm nút đóng ở đây -->
        <button type="button" class="close-form-btn" onclick="closeOrderForm()">
            <img src="../asset/images/icon/close.png" alt="Đóng">
        </button>

        <form id="orderFormElement" action="" method="post" class="order-form">
            <h2>Đặt hàng sản phẩm</h2>

            <label for="name">Tên của bạn:</label>
            <input type="text" id="name" name="name" required placeholder="Nhập tên của bạn" title="Vui lòng nhập tên của bạn" 
                value="<?php echo htmlspecialchars($user_info['fullname'] ?? ''); ?>">

            <label for="address">Địa chỉ giao hàng:</label>
            <input type="text" id="address" name="address" required placeholder="Nhập địa chỉ giao hàng" title="Vui lòng nhập địa chỉ giao hàng"
                value="<?php echo htmlspecialchars($user_info['address'] ?? ''); ?>">

            <label for="phone">Số điện thoại:</label>
            <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" inputmode="numeric" required placeholder="0123456789" title="Vui lòng nhập số điện thoại gồm 10 chữ số"
                value="<?php echo htmlspecialchars($user_info['phone'] ?? ''); ?>">

            <!-- nút muốn thanh toán loại nào -->
            <div class="payment-method-group">
                <p class="payment-method-title">Phương thức thanh toán:</p>
                <div class="payment-method-options">
                    <div class="payment-method-option">
                        <input type="radio" id="cod" name="paymentMethod" value="Thanh toán khi nhận hàng">
                        <span class="radio-custom"></span>
                        <label for="cod">Thanh toán khi nhận hàng</label>
                    </div>
                    <div class="payment-method-option">
                        <input type="radio" id="bank" name="paymentMethod" value="Chuyển khoản">
                        <span class="radio-custom"></span>
                        <label for="bank">Chuyển khoản ngân hàng</label>
                    </div>
                </div>
            </div>

            <!-- Hiển thị tổng số tiền -->
            <label for="name-invoice-form" class="total-amount nameInForm" style="display: none">
                Tên thú cưng: <span id="name-invoice-form"></span>
            </label>
            <label for="total-amount-form" class="total-amount">
                Tổng số tiền: <span id="total-amount-form">0đ</span>
            </label>

            <?php if ($is_logged_in): ?>
            <!-- nút gửi -->
            <button type="button" class="btn-submit">
                <img src="../asset/images/icon/take-form.png" alt="Gửi">
            </button>
            <?php else: ?>
            <button type="button" class="btn-submit" style="display: none">
                <img src="../asset/images/icon/take-form.png" alt="Gửi">
            </button>
            <?php endif; ?>
        </form>
    </div>
    <div id="bankModal" class="modal-bank" style="display: none;">
        <div class="modal-bank-content">
            <span class="close">&times;</span>
            <h3>Thông tin chuyển khoản ngân hàng</h3>
            <p>Vui lòng chuyển khoản tới tài khoản sau:</p>
            <p><strong>Ngân hàng:</strong> Vietcombank</p>
            <p><strong>Số tài khoản:</strong> 123456789</p>
            <p><strong>Chủ tài khoản:</strong> Nguyễn Văn A</p>
            <p><strong>Nội dung chuyển khoản:</strong> Thanh toán đơn hàng ABC123</p>
            <p><strong>Chủ tài khoản:</strong> Thực hiện thanh toán vào ngay tài khoản ngân hàng của chúng tôi. Vui lòng sử dụng Mã đơn hàng của bạn trong phần Nội dung thanh toán. Đơn hàng sẽ được giao sau khi tiền đã chuyển.</p>
            <button id="confirm-payment">Xác nhận đã chuyển khoản</button>
        </div>
    </div>
    <div id="popup-notification-invoice" class="popup-notification">
                <p id="popup-message-invoice"></p>
            </div>
</div>

<script src="../asset/js/form-cart.js"></script>
<script src="../asset/js/cart.js"></script>