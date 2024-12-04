<?php
require 'config.php';

session_start();
// Gửi header JSON
header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

// Kiểm tra xem có phải là yêu cầu POST không
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Validate and sanitize input
        $name = htmlspecialchars(strip_tags($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars(strip_tags($_POST['address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars(strip_tags($_POST['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $payment = htmlspecialchars(strip_tags($_POST['paymentMethod'] ?? ''), ENT_QUOTES, 'UTF-8');
        $total_amount = filter_var(str_replace(['đ', ','], '', $_POST['total_amount'] ?? '0'), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $pet_ids = json_decode($_POST['pet_ids'] ?? '[]', true);
        $pet_quantities = json_decode($_POST['pet_quantities'] ?? '[]', true);
        $gender = json_decode($_POST['gender'] ?? '[]', true);

        // Đảm bảo các giá trị là mảng
        $pet_ids = is_array($pet_ids) ? $pet_ids : [];
        $pet_quantities = is_array($pet_quantities) ? $pet_quantities : [];
        $gender = is_array($gender) ? $gender : [];

        if (empty($name) || empty($address) || empty($phone) || empty($payment) || empty($pet_ids) || empty($pet_quantities) || empty($gender)) {
            throw new Exception("Vui lòng điền đầy đủ thông tin.");
        }

        // Kiểm tra trạng thái đăng nhập
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $is_logged_in = true;

            // kiểm tra user có thông tin cá nhân hay không
            $stmt = $conn->prepare("SELECT fullname, address, phone FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($user['fullname']) || empty($user['address']) || empty($user['phone'])) {
                // nhập thông tin cá nhân từ form
                $stmt = $conn->prepare("UPDATE users SET fullname = ?, address = ?, phone = ? WHERE id = ?");
                $stmt->execute([$name, $address, $phone, $user_id]);
            }
        } else {
            $is_logged_in = false;
            
            // Kiểm tra xem đã có guest_id trong cookie chưa
            $guest_id = $_COOKIE['guest_id'] ?? bin2hex(random_bytes(16));
            setcookie('guest_id', $guest_id, time() + (86400 * 30), '/', '', true, true);

            // Xử lý đơn hàng cho khách
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $guest_email = $guest_id . '@guest.com';
            $stmt->execute([$guest_email]);
            $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing_user) {
                $user_id = $existing_user['id'];
            } else {
                $stmt = $conn->prepare("INSERT INTO users (username, fullname, email, phone, address, pass, idgroup) VALUES ('guest', ?, ?, ?, ?, ?, 0)");
                $stmt->execute([ $name, $guest_email, $phone, $address, password_hash($guest_id, PASSWORD_DEFAULT)]);
                $user_id = $conn->lastInsertId();
            }
        }

        // Create new order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, totalAmount, status, payment) VALUES (?, ?, ?, ?)");
        $status = 'Đang xử lý';
        $stmt->execute([$user_id, $total_amount, $status, $payment]);
        $order_id = $conn->lastInsertId();

        // Add order details
        $stmt = $conn->prepare("INSERT INTO order_details (order_id, pet_id, genderOrder, quantity, price) VALUES (?, ?, ?, ?, ?)");
        
        // Đảm bảo $pet_ids, $pet_quantities và $gender có cùng độ dài
        $count = min(count($pet_ids), count($pet_quantities), count($gender));

        for ($i = 0; $i < $count; $i++) {
            $pet_id = $pet_ids[$i];
            $quantity = $pet_quantities[$i];
            $genderOrder = $gender[$i]; // Đổi tên biến để tránh xung đột

            $pet_stmt = $conn->prepare("SELECT price, priceSale FROM pets WHERE id = ?");
            $pet_stmt->execute([$pet_id]);
            $pet = $pet_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($pet) {
                $price = $pet['priceSale'] > 0 ? $pet['priceSale'] : $pet['price'];
                $stmt->execute([$order_id, $pet_id, $genderOrder, $quantity, $price]);
                
                if ($is_logged_in) {
                    $delete_stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = (SELECT cart_id FROM cart WHERE user_id = ?) AND pet_id = ?");
                    $delete_stmt->execute([$user_id, $pet_id]);
                }
            } else {
                // Log hoặc xử lý trường hợp không tìm thấy pet
                error_log("Không tìm thấy pet với ID: $pet_id");
            }
        }
        
        // Xóa các mục đã đặt hàng khỏi giỏ hàng của khách
        if (isset($_COOKIE['guest_cart'])) {
            $cart = json_decode(gzuncompress(base64_decode($_COOKIE['guest_cart'])), true);
            
            foreach ($pet_ids as $pet_id) {
                if (isset($cart[$pet_id])) {
                    unset($cart[$pet_id]);
                }
            }
            
            // Mã hóa và nén giỏ hàng mới
            $encoded_cart = base64_encode(gzcompress(json_encode($cart)));
            
            // Cập nhật cookie với giỏ hàng mới
            setcookie('guest_cart', $encoded_cart, time() + (86400 * 30), '/', '', true, true);
        }

        $conn->commit();
        
        $response['success'] = true;
        $response['message'] = 'Đơn hàng đã được tạo thành công!';
        $response['order_id'] = $order_id;
    } catch (Exception $e) {
        $conn->rollBack();
        $response['message'] = 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage();
        error_log($e->getMessage());
    }
} else {
    $response['message'] = 'Phương thức không được hỗ trợ.';
}

echo json_encode($response);
