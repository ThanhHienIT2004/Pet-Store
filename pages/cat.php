<?php
// Khai báo các thông số kết nối cơ sở dữ liệu
require '../config/config.php';

try {
    // Thực hiện truy vấn để lấy tất cả các sản phẩm thuộc danh mục 'cat'
    $stmt = $conn->prepare("SELECT * FROM pets WHERE idLoai = :idLoai");
    $stmt->bindParam(':idLoai', $idLoai);
    $idLoai = 'cat'; // Danh mục cần lọc
    $stmt->execute();

    // Lưu kết quả truy vấn vào một mảng
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<h2>Mèo</h2>
<div class="pets-grid">
    <?php if (!empty($pets)): ?>
    <?php foreach ($pets as $pet): ?>

    <div class="container-pets">
        <img src="<?php echo htmlspecialchars($pet['urlImg']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
        <div class="row">
            <p class="name-pet"><?php echo htmlspecialchars($pet['name']); ?></p>
            <div class="icons">
                <button class="button view-details" data-id="<?php echo htmlspecialchars($pet['id']); ?>">Chi tiết</button>
                <button class="button order"
                    onclick="addToPet('<?php echo htmlspecialchars($pet['id'], ENT_QUOTES, 'UTF-8'); ?>')">Giỏ
                    hàng</button>
            </div> 
        </div>
        <p class="text-price">Giá: <span class="price"><?php echo number_format($pet['price'], 0, ',', '.'); ?>đ</span>
            ➱
            <?php echo number_format($pet['priceSale'], 0, ',', '.'); ?>đ</p>
    </div>

    <?php endforeach; ?>
    <?php else: ?>
    <p>Chưa có sản phẩm nào.</p>
    <?php endif; ?>
</div>