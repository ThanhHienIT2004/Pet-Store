<?php
// Kết nối cơ sở dữ liệu
$host = "localhost";
$dbname = "pet-store";
$userdb = "root";
$passdb = "";
$conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $userdb, $passdb);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Hàm lấy danh sách thú cưng từ bảng pets
function layDanhSachPets() {
    global $conn;
    $sql = "SELECT id, name, idLoai, price, priceSale, gender, quantity, urlImg, hot, description FROM pets";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm thêm thú cưng vào bảng pets
function themPets($id, $name, $price, $priceSale, $gender, $quantity, $urlImg, $idLoai, $description, $hot) {
    global $conn;
    $sql = "INSERT INTO pets (id, name, price, priceSale, gender, quantity, urlImg, idLoai, description, hot) 
            VALUES ('$id', '$name', $price, $priceSale, $gender, $quantity, '$urlImg', '$idLoai', '$description', $hot)";
    $kq = $conn->exec($sql);
    return $kq;
}

// Hàm xóa thú cưng khỏi bảng pets
function xoaPets($id) {
    global $conn;
    $sql = "DELETE FROM pets WHERE id = '$id'";
    $kq = $conn->exec($sql);
    return $kq;
}

// Hàm lấy chi tiết thú cưng từ bảng pets
function layChiTietPets($id) {
    global $conn;
    $sql = "SELECT id, name, price, priceSale, gender, quantity, urlImg, idLoai, description, hot 
            FROM pets WHERE id = '$id'";
    $kq = $conn->query($sql);
    if ($kq == null) {
        return false;
    } else {
        return $kq->fetch();
    }
}

// Hàm cập nhật thông tin thú cưng
function capnhatPets($id, $name, $price, $priceSale, $gender, $quantity, $urlImg, $idLoai, $description, $hot) {
    global $conn;
    $sql = "UPDATE pets SET name = '$name', price = $price, priceSale = $priceSale, gender = $gender, 
            quantity = $quantity, urlImg = '$urlImg', idLoai = '$idLoai', description = '$description', hot = $hot 
            WHERE id = '$id'";
    $kq = $conn->exec($sql);
    return $kq;
}
function layDanhSachDonHang() {
    global $conn;
    // Truy vấn để lấy danh sách đơn hàng
    $sql = "SELECT o.idOrder AS order_id, o.orderDate, o.status AS order_status, o.payment,
    SUM(od.quantity * od.price) AS total_price,
        u.fullname, u.phone, u.address, o.payment
    FROM orders o
    JOIN order_details od ON o.idOrder = od.order_id
    JOIN pets p ON od.pet_id = p.id
    JOIN users u ON o.user_id = u.id
    GROUP BY o.idOrder, o.orderDate, o.status, u.fullname, u.phone, u.address
    ORDER BY o.idOrder";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $orders;
}

// Hàm xóa đơn hàng
function xoaDonHang($order_id) {
    global $conn;
    try {
        // Bắt đầu giao dịch
        $conn->beginTransaction();

        // Xóa chi tiết đơn hàng trước
        $sql_details = "DELETE FROM order_details WHERE order_id = :order_id";
        $stmt_details = $conn->prepare($sql_details);
        $stmt_details->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt_details->execute();

        // Sau đó xóa đơn hàng
        $sql_order = "DELETE FROM orders WHERE idOrder = :order_id";
        $stmt_order = $conn->prepare($sql_order);
        $stmt_order->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result = $stmt_order->execute();

        // Hoàn tất giao dịch
        $conn->commit();

        return $result;
    } catch (PDOException $e) {
        // Nếu có lỗi, rollback giao dịch
        $conn->rollBack();
        error_log("Lỗi xóa đơn hàng: " . $e->getMessage());
        return false;
    }
}

