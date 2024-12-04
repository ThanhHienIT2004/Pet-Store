<?php
require 'config.php';
session_start();

$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id']);

if (isset($_POST['id']) && isset($_POST['quantity'])) {
    $pet_id = $_POST['id'];
    $quantity = $_POST['quantity'];

    if ($quantity < 1) {
        echo json_encode(['success' => false, 'message' => 'Số lượng không hợp lệ.']);
        exit;
    }

    try {
        if ($is_logged_in) {
            // Xử lý cho người dùng đã đăng nhập
            $user_id = $_SESSION['user_id'];
            
            // Lấy cart_id
            $stmt = $conn->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $cart = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cart) {
                throw new Exception("Không tìm thấy giỏ hàng.");
            }

            $cart_id = $cart['cart_id'];

            // Kiểm tra xem bản ghi có tồn tại
            $stmt = $conn->prepare("SELECT COUNT(*) FROM cart_items WHERE cart_id = ? AND pet_id = ?");
            $stmt->execute([$cart_id, $pet_id]);
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                // Nếu không tồn tại, thêm mới
                $stmt = $conn->prepare("INSERT INTO cart_items (cart_id, pet_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$cart_id, $pet_id, $quantity]);
            } else {
                // Nếu tồn tại, cập nhật
                $stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND pet_id = ?");
                $stmt->execute([$quantity, $cart_id, $pet_id]);
                $affectedRows = $stmt->rowCount();
                if ($affectedRows == 0) {
                    throw new Exception("Không có bản ghi nào được cập nhật.");
                }
            }

            // Lấy thông tin giỏ hàng mới từ database
            $stmt = $conn->prepare("
                SELECT pets.id, pets.name, pets.price, pets.priceSale, pets.urlImg, cart_items.quantity, cart_items.price as item_price
                FROM cart_items 
                JOIN pets ON cart_items.pet_id = pets.id 
                WHERE cart_items.cart_id = ?
            ");
            $stmt->execute([$cart_id]);
            $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Xử lý giỏ hàng cho khách không đăng nhập
            $cart = isset($_COOKIE['guest_cart']) ? json_decode(gzuncompress(base64_decode($_COOKIE['guest_cart'])), true) : [];
        
            // Lấy thông tin sản phẩm từ database
            $stmt = $conn->prepare("SELECT id, name, price, priceSale, urlImg FROM pets WHERE id = ?");
            $stmt->execute([$pet_id]);
            $pet = $stmt->fetch(PDO::FETCH_ASSOC);
        
            if (!$pet) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm.']);
                exit;
            }
        
            $price = $pet['priceSale'] > 0 ? $pet['priceSale'] : $pet['price'];
            $cart[$pet_id] = [
                'pet_id' => $pet['id'],
                'name' => $pet['name'],
                'price' => $price,
                'urlImg' => $pet['urlImg'],
                'quantity' => $quantity
            ];
        
            // Cập nhật cookie với giỏ hàng mới
            $encoded_cart = base64_encode(gzcompress(json_encode($cart)));
            setcookie('guest_cart', $encoded_cart, time() + (86400 * 30), '/', '', true, true);
        
            $cartItems = array_values($cart);
        }

        // Tính tổng giá trị giỏ hàng
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }

        $_SESSION['cart-items'] = $cartItems;

        echo json_encode(['success' => true, 'totalAmount' => number_format($totalAmount, 0, ',', '.') . 'đ']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
}