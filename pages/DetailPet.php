<?php
// Kết nối cơ sở dữ liệu
require '../config/config.php';

if (isset($_GET['action']) && $_GET['action'] === 'getPetDetails' && isset($_GET['id'])) {
    $petId = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM pets WHERE id = :id");
        $stmt->bindParam(':id', $petId);
        $stmt->execute();

        $pet = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pet) {
            echo json_encode($pet);
        } else {
            echo json_encode(['error' => 'Pet not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}
?>

<link href="../asset/css/DetailPet.css" rel="stylesheet">

<div id="modal" class="custom-modal">
    <div class="custom-modal-content">
        <div class="detail-img-container">
            <img class="custom-modal-img" src="<?php echo htmlspecialchars($pet['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($pet['name'] ?? ''); ?>">
        </div>
        <div>
            <div class="detail-info-container">
                <h1 class="custom-modal-title"><?php echo htmlspecialchars($pet['name'] ?? ''); ?></h1>

                <p class="custom-modal-price">Giá: <?php echo number_format($pet['price'] ?? 0, 0, ',', '.'); ?>đ</p>
                <p class="custom-modal-sale-price">Giá khuyến mãi: <?php echo number_format($pet['sale_price'] ?? 0, 0, ',', '.'); ?>đ</p>
                <p class="custom-modal-quantity">Số lượng còn lại: <?php echo number_format($pet['quantity'] ?? 0, 0, ',', '.'); ?></p>
                <p class="custom-modal-description">Mô tả: <?php echo htmlspecialchars($pet['description'] ?? ''); ?></p>
            </div>
            
            <div class="detail-btn-container">
                <button class="add-to-cart" onclick="addToPet(this.getAttribute('data-pet-id'))">Giỏ hàng</button>
            </div>
        </div>
        <span class="custom-close">&times;</span>   
    </div>
</div>

<script src="../asset/js/cart.js"></script>

