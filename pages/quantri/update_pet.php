_<?php // Đảm bảo bắt đầu phiên làm việc

// Kết nối đến cơ sở dữ liệu
require_once 'functions.php'; // Đảm bảo đường dẫn chính xác

if (isset($_POST['btn'])) {
    // Tiếp nhận dữ liệu từ form
    $id = (int)$_POST['id'];
    $name = trim(strip_tags($_POST['name']));
    $type = trim(strip_tags($_POST['type']));
    $price = (float)$_POST['price'];

    // Cập nhật dữ liệu vào cơ sở dữ liệu
    $sql = "UPDATE pets SET name = :name, idLoai = :type, price = :price WHERE id = :id";
    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':type' => $type,
        ':price' => $price
    ]);

    if ($result) {
        header("Location: index.php?page=danhmucpets_ds"); // Chuyển hướng đến danh sách thú cưng
        exit();
    } else {
        $_SESSION['thongbao'] = "Lỗi khi cập nhật thú cưng. Vui lòng thử lại.";
        header("Location: edit_pet.php?id=" . urlencode($id)); // Quay lại trang sửa
        exit();
    }
}
?>
