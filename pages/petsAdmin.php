<?php
// Nhúng tệp config.php để kết nối cơ sở dữ liệu
require '../config/config.php';

try {
    // Tạo đối tượng PDO để kết nối với cơ sở dữ liệu MySQL
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Thực hiện truy vấn để lấy tất cả các loại sản phẩm
    $stmt = $conn->prepare("SELECT DISTINCT idLoai FROM pets");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Khởi tạo mảng để lưu trữ sản phẩm theo loại
    $petsByCategory = [];

    foreach ($categories as $category) {
        $stmt = $conn->prepare("SELECT * FROM pets WHERE idLoai = :idLoai");
        $stmt->bindParam(':idLoai', $category);
        $stmt->execute();
        $petsByCategory[$category] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<link rel="stylesheet" href="../asset/css/petsAdmin.css">

<?php foreach ($petsByCategory as $category => $pets): ?>
<div class="category-section">
    <h2>Loại sản phẩm: <?php echo htmlspecialchars($category); ?></h2>
    <div class="pets-admin-grid">
        <?php if (!empty($pets)): ?>
        <?php foreach ($pets as $pet): ?>
        <div class="container-pets-admin" data-id="<?php echo htmlspecialchars($pet['id']); ?>"
            data-idLoai="<?php echo htmlspecialchars($pet['idLoai']); ?>">
            <img class="img-pet" src="<?php echo htmlspecialchars($pet['urlImg']); ?>"
                alt="<?php echo htmlspecialchars($pet['name']); ?>">
            <div class="scrollable-container">
                <div class="row">
                    <label for="name-<?php echo htmlspecialchars($pet['id']); ?>">Tên:</label>
                    <input type="text" class="edit-name" id="name-<?php echo htmlspecialchars($pet['id']); ?>"
                        value="<?php echo htmlspecialchars($pet['name']); ?>">
                </div>
                <div class="row">
                    <label for="price-<?php echo htmlspecialchars($pet['id']); ?>">Giá:</label>
                    <input type="number" class="edit-price" id="price-<?php echo htmlspecialchars($pet['id']); ?>"
                        value="<?php echo htmlspecialchars($pet['price']); ?>">
                </div>
                <div class="row">
                    <label for="priceSale-<?php echo htmlspecialchars($pet['id']); ?>">Giá giảm:</label>
                    <input type="number" class="edit-priceSale"
                        id="priceSale-<?php echo htmlspecialchars($pet['id']); ?>"
                        value="<?php echo htmlspecialchars($pet['priceSale']); ?>">
                </div>
                <div class="row">
                    <label for="gender-<?php echo htmlspecialchars($pet['id']); ?>">Giới tính:</label>
                    <input name="gender" type="radio" value="1" <?php echo intval($pet['gender']) === 1 ? 'checked' : ''; ?> /> Đực
                    <input name="gender" type="radio" value="0" <?php echo intval($pet['gender']) === 0 ? 'checked' : ''; ?> /> Cái


                    
                </div>
                
                <div class="row">
                    <label for="description-<?php echo htmlspecialchars($pet['id']); ?>">Mô tả:</label>
                    <textarea class="edit-description" id="description-<?php echo htmlspecialchars($pet['id']); ?>"
                        rows="4"><?php echo htmlspecialchars($pet['description']); ?></textarea>
                </div>
                <div class="row">
                    <label for="urlImg-<?php echo htmlspecialchars($pet['id']); ?>">Chọn Ảnh:</label>
                    <input type="file" class="edit-urlImg" id="urlImg-<?php echo htmlspecialchars($pet['id']); ?>"
                        name="pet-image-<?php echo htmlspecialchars($pet['id']); ?>">
                </div>
            </div>
            <div class="">
                <button class="save-btn-pets-admin">Lưu</button>
                <button class="cancel-btn">Xóa</button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p>Chưa có sản phẩm nào.</p>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>


<script src="../asset/js/admin.js"></script>