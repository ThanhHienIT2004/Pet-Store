<?php
// Kết nối đến cơ sở dữ liệu
require '../config/config.php';

try {
    // Thực hiện truy vấn để lấy tất cả các sản phẩm thuộc danh mục 'parrot'
    $stmt = $conn->prepare("SELECT * FROM pets WHERE idLoai = :idLoai");
    $stmt->bindParam(':idLoai', $idLoai);
    $stmt->execute();

    // Lưu kết quả truy vấn vào một mảng
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kiểm tra nếu từ khóa tìm kiếm được gửi đi
    if (isset($_GET['tukhoa'])) {
        $tukhoa = $_GET['tukhoa'];

        // Truy vấn tìm kiếm dựa trên từ khóa sử dụng prepared statement để tránh SQL injection
        $stmt = $conn->prepare("SELECT * FROM pets WHERE name LIKE :keyword OR id LIKE :keyword");
        $likeKeyword = "%$tukhoa%";
        $stmt->bindParam(':keyword', $likeKeyword);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hiển thị tiêu đề kết quả tìm kiếm
        echo "<h2>Kết quả tìm kiếm cho từ khóa: '" . htmlspecialchars($tukhoa, ENT_QUOTES, 'UTF-8') . "'</h2>";

        if (count($result) > 0) {
            echo '<div class="pets-grid">'; // Bắt đầu bao ngoài cho tất cả các sản phẩm
            foreach ($result as $row) {
                // Gọi hàm để hiển thị sản phẩm
                displayPet(
                    $row['urlImg'],
                    $row['name'],
                    $row['id'],
                    $row['price'],
                    $row['priceSale']
                );
            }
            echo '</div>'; // Kết thúc lớp bao ngoài
        } else {
            echo "<p>Không tìm thấy kết quả nào.</p>";
        }
    } else {
        echo "<p>Vui lòng nhập từ khóa để tìm kiếm.</p>";
    }

} catch (PDOException $e) {
    echo "Kết nối thất bại: " . $e->getMessage();
}

// Hàm hiển thị sản phẩm
function displayPet($urlImg, $name, $id, $price, $priceSale) {
    ?>
    <div class="pet-item">
        <div class="container-pets">
            <img src="<?php echo htmlspecialchars($urlImg, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="row">
                <p class="name-pet"><?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?></p>
                <div class="icons">
                    <button class="button view-details" data-id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>">Chi tiết</button>
                    <button class="button order" onclick="addToPet('<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8'); ?>')">Giỏ hàng</button>
                </div>
            </div>
            <p class="text-price">Giá: <span class="price"><?php echo number_format($price, 0, ',', '.'); ?>đ</span>
                <?php if (!empty($priceSale)) { ?>
                    ➱ <?php echo number_format($priceSale, 0, ',', '.'); ?>đ
                <?php } ?>
            </p>
        </div>
    </div>
    <?php
}
?>
