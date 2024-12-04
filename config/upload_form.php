<?php
// Nhúng tệp config.php để kết nối cơ sở dữ liệu
require '../config/config.php';

try {
    // Tạo đối tượng PDO để kết nối với cơ sở dữ liệu MySQL
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kiểm tra nếu form được gửi bằng phương thức POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Lấy dữ liệu từ form
        $id = $_POST['pet-id'];
        $name = $_POST['pet-name'];
        $price = $_POST['pet-price'];
        $priceSale = $_POST['pet-price-sale'];
        $gender = isset($_POST['pet-gender']) ? intval($_POST['pet-gender']) : 0; // Nhận giá trị giới tính, mặc định là 0
        $quantity = $_POST['pet-quantity'];
        $idLoai = $_POST['pet-idLoai'];  // Nhận giá trị từ select
        $description = $_POST['pet-description']; // Nhận giá trị mô tả

        // Xử lý file upload nếu có
        $urlImg = null; // Giá trị mặc định nếu không có hình ảnh mới
        if (isset($_FILES['pet-image']) && $_FILES['pet-image']['error'] == UPLOAD_ERR_OK) {
            $imageName = basename($_FILES["pet-image"]["name"]);
            $targetDir = "../asset/uploads/";  // Thư mục lưu trữ hình ảnh với đường dẫn tương đối
            $targetFile = $targetDir . $imageName;

            // Xác thực tệp
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                die("Chỉ hỗ trợ các định dạng hình ảnh: jpg, jpeg, png, gif.");
            }

            if ($_FILES["pet-image"]["size"] > 5000000) { // Ví dụ: 5MB
                die("Kích thước tệp quá lớn.");
            }

            // Kiểm tra sự tồn tại của thư mục và tạo nếu không tồn tại
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Kiểm tra và di chuyển file upload vào thư mục lưu trữ
            if (move_uploaded_file($_FILES["pet-image"]["tmp_name"], $targetFile)) {
                $urlImg = $targetFile;
            } else {
                die("Có lỗi xảy ra khi tải lên hình ảnh.");
            }
        }

        // Kiểm tra nếu ID đã tồn tại trong cơ sở dữ liệu
        $checkIdStmt = $conn->prepare("SELECT COUNT(*) FROM pets WHERE id = :id");
        $checkIdStmt->bindParam(':id', $id);
        $checkIdStmt->execute();

        if ($checkIdStmt->fetchColumn() > 0) {
            // Cập nhật sản phẩm nếu ID đã tồn tại
            $query = "UPDATE pets SET name = :name, price = :price, priceSale = :priceSale, 
                      gender = :gender, quantity = :quantity, idLoai = :idLoai, description = :description" .
                ($urlImg ? ", urlImg = :urlImg" : "") . " WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':priceSale', $priceSale);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':idLoai', $idLoai);
            $stmt->bindParam(':description', $description);

            if ($urlImg) {
                $stmt->bindParam(':urlImg', $urlImg);
            }

            if ($stmt->execute()) {
                header('Location: ../pages/index.php?page=admin&table=pets');
                exit();
            } else {
                echo 'Có lỗi xảy ra khi cập nhật sản phẩm.';
            }
        } else {
            // Thêm sản phẩm mới nếu ID không tồn tại
            $query = "INSERT INTO pets (id, name, price, priceSale, gender, quantity, idLoai, description, urlImg) 
                      VALUES (:id, :name, :price, :priceSale, :gender, :quantity, :idLoai, :description, :urlImg)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':priceSale', $priceSale);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->bindParam(':idLoai', $idLoai);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':urlImg', $urlImg);

            if ($stmt->execute()) {
                echo "<script>alert('Cập nhật thành công');</script>";
                exit();
            } else {
                echo 'Có lỗi xảy ra khi thêm sản phẩm.';
            }
        }
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}