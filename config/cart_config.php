<?php
require 'config.php';

session_start();
// Kiểm tra trạng thái đăng nhập
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
    // Người dùng đã đăng nhập, sử dụng user_id từ session
    $user_id = $_SESSION['user_id'];
    $is_logged_in = true;
} else {
    // Người dùng chưa đăng nhập
    $is_logged_in = false;
    
    // Kiểm tra xem đã có guest_id trong cookie chưa
    if (!isset($_COOKIE['guest_id'])) {
        // Nếu chưa có, tạo một guest_id mới
        $guest_id = bin2hex(random_bytes(16)); // Tạo một chuỗi ngẫu nhiên 32 ký tự
        
        // Lưu guest_id vào cookie, có thời hạn 30 ngày
        setcookie('guest_id', $guest_id, time() + (86400 * 30), '/', '', true, true);
    } else {
        // Nếu đã có, sử dụng guest_id từ cookie
        $guest_id = $_COOKIE['guest_id'];
    }
    
    $user_id = $guest_id;
    
    // Không cần khởi tạo $_SESSION['cart'] nữa vì chúng ta sử dụng cookie
}

// Gửi header JSON
header('Content-Type: application/json');

// Lấy pet_id từ POST request
if (isset($_POST['action']) && isset($_POST['pet_id'])) {
    $action = $_POST['action'];
    $pet_id = $_POST['pet_id'];

    $response = array(
        'success' => false, 
        'message' => '',
        'session_info' => array(
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            'guest_id' => isset($_SESSION['guest_id']) ? $_SESSION['guest_id'] : null,
            'logged_in' => isset($_SESSION['logged_in']) ? $_SESSION['logged_in'] : false
        )
    );

    switch ($action) {
        case 'add':
            if ($is_logged_in) {
                $result = addToCart($user_id, $pet_id, 1, $conn);
            } else {
                $result = addToGuestCart($pet_id, $conn);
            }
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Sản phẩm đã được thêm vào giỏ hàng!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!';
            }
            break;
        case 'remove':
            if ($is_logged_in) {
                $result = removeFromCart($user_id, $pet_id, $conn);
            } else {
                $result = removeFromGuestCart($pet_id);
            }
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Sản phẩm đã được xóa khỏi giỏ hàng!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng!';
            }
            break;
        default:
            $response['message'] = 'Hành động không hợp lệ!';
    }

    // Cập nhật thông tin giỏ hàng trong response
    if ($is_logged_in) {
        $cartItems = getCartItems($user_id, $conn);
        if ($cartItems !== false) {
            $response['cart_items'] = $cartItems;
        }
    } else {
        $response['cart_items'] = getGuestCart();
    }

    echo json_encode($response);
} else {
    echo json_encode(array('success' => false, 'message' => 'Thiếu thông tin cần thiết!'));
}

function addToCart($user_id, $pet_id, $quantity = 1, $conn)
{
    try {
        $conn->beginTransaction();

        // Kiểm tra xem người dùng đã có giỏ hàng chưa
        $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            // Nếu chưa có giỏ hàng, tạo mới
            $stmt = $conn->prepare("INSERT INTO cart (user_id) VALUES (?)");
            $stmt->execute([$user_id]);
            $cart_id = $conn->lastInsertId();
            error_log("Created new cart with ID: " . $cart_id);
        } else {
            $cart_id = $cart['cart_id'];
            error_log("Using existing cart with ID: " . $cart_id);
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $stmt = $conn->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND pet_id = ?");
        $stmt->execute([$cart_id, $pet_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        // Lấy giá của sản phẩm
        $stmt = $conn->prepare("SELECT price, priceSale FROM pets WHERE id = ?");
        $stmt->execute([$pet_id]);
        $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pet) {
            throw new Exception("Pet not found with ID: " . $pet_id);
        }
        $price = $pet['priceSale'] > 0 ? $pet['priceSale'] : $pet['price'];

        if ($item) {
            // Nếu sản phẩm đã có trong giỏ, cộng thêm số lượng
            $new_quantity = $item['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart_items SET quantity = ?, price = ? WHERE id = ?");
            $stmt->execute([$new_quantity, $price, $item['id']]);
            error_log("Updated existing cart item. New quantity: " . $new_quantity);
        } else {
            // Nếu sản phẩm chưa có trong giỏ, thêm mới
            $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, pet_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cart_id, $pet_id, $quantity, $price]);
            error_log("Added new item to cart. Pet ID: " . $pet_id . ", Quantity: " . $quantity);
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error in addToCart: " . $e->getMessage());
        return false;
    }
}

function removeFromCart($user_id, $pet_id, $conn)
{
    try {
        // Lấy cart_id
        $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $cart = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cart) {
            throw new Exception("Không tìm thấy giỏ hàng.");
        }

        $cart_id = $cart['cart_id'];

        // Xóa sản phẩm khỏi giỏ hàng
        $stmt = $conn->prepare("DELETE FROM cart_items WHERE cart_id = ? AND pet_id = ?");
        $stmt->execute([$cart_id, $pet_id]);

        return true;
    } catch (Exception $e) {
        error_log("Error in removeFromCart: " . $e->getMessage());
        return false;
    }
}

function getCartItems($user_id, $conn)
{
    try {
        $stmt = $conn->prepare("
            SELECT c.cart_id, ci.pet_id, ci.quantity, ci.price as item_price, 
                   p.name, p.price, p.priceSale, p.urlImg
            FROM cart c
            JOIN cart_items ci ON c.cart_id = ci.cart_id
            JOIN pets p ON ci.pet_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getCartItems: " . $e->getMessage());
        return false;
    }
}

function addToGuestCart($pet_id, $conn) {
    $stmt = $conn->prepare("SELECT id, name, price, priceSale, urlImg FROM pets WHERE id = ?");
    $stmt->execute([$pet_id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pet) {
        $price = $pet['priceSale'] > 0 ? $pet['priceSale'] : $pet['price'];
        
        // Lấy giỏ hàng hiện tại từ cookie (nếu có)
        $cart = isset($_COOKIE['guest_cart']) ? json_decode(gzuncompress(base64_decode($_COOKIE['guest_cart'])), true) : [];
        
        if (isset($cart[$pet_id])) {
            $cart[$pet_id]['quantity']++;
        } else {
            $cart[$pet_id] = array(
                'pet_id' => $pet['id'],
                'name' => $pet['name'],
                'price' => $price,
                'urlImg' => $pet['urlImg'],
                'quantity' => 1,
                'genderCart' => 0
            );
        }
        
        // Mã hóa và nén giỏ hàng trước khi lưu vào cookie
        $encoded_cart = base64_encode(gzcompress(json_encode($cart)));
        
        // Lưu giỏ hàng vào cookie, có thời hạn 30 ngày
        setcookie('guest_cart', $encoded_cart, time() + (86400 * 30), '/', '', true, true);
        
        return true;
    }
    return false;
}

function getGuestCart() {
    if (isset($_COOKIE['guest_cart'])) {
        return json_decode(gzuncompress(base64_decode($_COOKIE['guest_cart'])), true);
    }
    return [];
}

function updateGuestCart($cart) {
    $encoded_cart = base64_encode(gzcompress(json_encode($cart)));
    setcookie('guest_cart', $encoded_cart, time() + (86400 * 30), '/', '', true, true);
}

function removeFromGuestCart($pet_id) {
    $cart = getGuestCart();
    if (isset($cart[$pet_id])) {
        unset($cart[$pet_id]);
        updateGuestCart($cart);
        return true;
    }
    return false;
}