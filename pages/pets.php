<?php
// Khai báo các thông số kết nối cơ sở dữ liệu
require '../config/config.php';

try {
    // Tạo đối tượng PDO để kết nối với cơ sở dữ liệu MySQL
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Thực hiện truy vấn để lấy tất cả các sản phẩm thuộc danh mục 'cat'
    $stmt = $conn->prepare("SELECT * FROM pets");

    $stmt->execute();

    // Lưu kết quả truy vấn vào một mảng
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<?php
try {
    // Tạo đối tượng PDO để kết nối với cơ sở dữ liệu MySQL
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Khởi tạo mảng các điều kiện
    $conditions = [];
    $params = [];

    // Lọc theo tên sản phẩm
    if (!empty($_GET['name'])) {
        $conditions[] = 'name LIKE :name';
        $params[':name'] = '%' . $_GET['name'] . '%';
    }

    // Lọc theo giá
    if (!empty($_GET['min_price'])) {
        $conditions[] = 'price >= :min_price';
        $params[':min_price'] = $_GET['min_price'];
    }
    if (!empty($_GET['max_price'])) {
        $conditions[] = 'price <= :max_price';
        $params[':max_price'] = $_GET['max_price'];
    }

    // Xây dựng câu truy vấn SQL
    $sql = "SELECT * FROM pets";
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    // Lưu kết quả truy vấn vào một mảng
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>



<div class="loc">
    <form id="filterForm" method="GET" action="">
        <div class="filter-container">
            <div class="filter-group">
                <label for="min_price">Giá tối thiểu</label>
                <select id="min_price" name="min_price">
                    <option value="">Chọn giá tối thiểu</option>
                    <?php for ($i = 0; $i <= 10; $i++): ?>
                        <?php $value = $i * 1000000; ?>
                        <option value="<?php echo $value; ?>" <?php echo isset($_GET['min_price']) && $_GET['min_price'] == $value ? 'selected' : ''; ?>>
                            <?php echo number_format($value, 0, ',', '.'); ?>đ
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="max_price">Giá tối đa</label>
                <select id="max_price" name="max_price">
                    <option value="">Chọn giá tối đa</option>
                    <?php for ($i = 0; $i <= 10; $i++): ?>
                        <?php $value = $i * 1000000; ?>
                        <option value="<?php echo $value; ?>" <?php echo isset($_GET['max_price']) && $_GET['max_price'] == $value ? 'selected' : ''; ?>>
                            <?php echo number_format($value, 0, ',', '.'); ?>đ
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="button-group">
            <button type="submit" class="btn-filter">Lọc</button>
            <button type="reset" class="btn-reset">Xóa</button>
        </div>
    </form>
</div>

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

