<?php
// Kết nối đến cơ sở dữ liệu
$host = "localhost";
$dbname = "pet-store";
$username = "root";
$password = "";

// Tạo kết nối
$conn = new mysqli($host, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy mã đơn hàng từ tham số URL
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
} else {
    die("Mã đơn hàng không được cung cấp.");
}

// Truy vấn thông tin đơn hàng và chi tiết của nó
$sql = "SELECT o.idOrder AS order_id, o.orderDate, o.status AS order_status, od.quantity, p.name AS pet_name, od.price AS pet_price
        FROM orders o
        JOIN order_details od ON o.idOrder = od.order_id
        JOIN pets p ON od.pet_id = p.id
        WHERE o.idOrder = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

// Kiểm tra nếu có kết quả
if ($result->num_rows > 0) {
    // Hiển thị thông tin đơn hàng
    echo "<div class='content'>";
    echo "<h2>Chi tiết đơn hàng #" . $order_id . "</h2>";
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<thead>";
    echo "<tr style='background-color: #f2f2f2;'>";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Sản phẩm</th>";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Số lượng</th>";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Giá</th>";
    echo "<th style='border: 1px solid #ddd; padding: 10px; text-align: left;'>Tổng tiền</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    $order_total_price = 0;

    while ($row = $result->fetch_assoc()) {
        $item_total_price = $row["quantity"] * $row["pet_price"];
        $order_total_price += $item_total_price; // Cộng dồn tổng tiền của đơn hàng

        echo "<tr>";
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $row["pet_name"] . "</td>"; // Tên sản phẩm
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . $row["quantity"] . "</td>"; // Số lượng
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . number_format($row["pet_price"], 0, ',', '.') . " VND</td>"; // Giá
        echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . number_format($item_total_price, 0, ',', '.') . " VND</td>"; // Tổng tiền
        echo "</tr>";
    }

    echo "<tr style='background-color: #f9f9f9;'>";
    echo "<td colspan='3' style='border: 1px solid #ddd; padding: 10px;'>Tổng đơn hàng</td>";
    echo "<td style='border: 1px solid #ddd; padding: 10px;'>" . number_format($order_total_price, 0, ',', '.') . " VND</td>";
    echo "</tr>";

    echo "</tbody>";
    echo "</table>";
    echo "<br><a href='index.php?page=index_user&pageuser=orders' style='padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;'>Trở về</a>";
    echo "</div>";
} else {
    echo "<p>Không có chi tiết cho đơn hàng này.</p>";
}

// Đóng kết nối
$stmt->close();
$conn->close();
?>
