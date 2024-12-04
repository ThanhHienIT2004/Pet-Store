<?php
require_once 'functions.php';

// Lấy id của thú cưng cần chỉnh sửa từ URL
$id = $_GET['id'] ?? '';
$id = htmlspecialchars($id);

// Lấy thông tin chi tiết của thú cưng từ cơ sở dữ liệu
$pet = layChiTietPets($id);

// Xử lý khi form được gửi
if (isset($_POST['btn'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $priceSale = $_POST['priceSale'] ?? null;
    $gender = $_POST['gender'];
    $quantity = $_POST['quantity'];
    $urlImg = $pet['urlImg']; // Giữ đường dẫn ảnh cũ
    $idLoai = $_POST['idLoai'];
    $description = $_POST['description'];
    $hot = $_POST['hot'] ?? 0;

    $uploadDir = '../../asset/uploads/'; // Đường dẫn tương đối đến thư mục uploads

    // Xử lý tệp hình ảnh mới
    if (isset($_FILES['urlImgNew']) && $_FILES['urlImgNew']['error'] == 0) {
        $fileTmpPath = $_FILES['urlImgNew']['tmp_name'];
        $fileName = basename($_FILES['urlImgNew']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Kiểm tra loại tệp (chỉ cho phép hình ảnh)
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExts)) {
            $newFileName = uniqid() . '.' . $fileExtension; // Tạo tên file mới để tránh trùng lặp
            $dest_path = $uploadDir . $newFileName;
            
            // Di chuyển tệp từ thư mục tạm thời đến thư mục uploads
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Xóa file ảnh cũ nếu tồn tại
                if (file_exists($uploadDir . basename($urlImg))) {
                    unlink($uploadDir . basename($urlImg));
                }
                $urlImg = '../asset/uploads/' . $newFileName; // Cập nhật đường dẫn mới
            } else {
                echo 'Có lỗi xảy ra khi tải lên hình ảnh mới.';
            }
        } else {
            echo 'Loại tệp không được phép. Chỉ cho phép hình ảnh.';
        }
    }

    settype($price, "float");
    settype($priceSale, "float");
    settype($gender, "int");
    settype($quantity, "int");
    settype($hot, "int");

    // Cập nhật thông tin thú cưng trong cơ sở dữ liệu
    $kq = capnhatPets($id, $name, $price, $priceSale, $gender, $quantity, $urlImg, $idLoai, $description, $hot);

    if ($kq) {
        header("location: index.php?page=pets_ds");
        exit();
    }
}
?>





<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa thú cưng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        h4 {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            font-size: 24px;
            color: #333;
            background-color: #f0f0f0;
            border-radius: 8px;
        }

        form {
            width: 40%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input[type="radio"] {
            margin-right: 5px;
        }

        .form-group img {
            max-width: 100px;
            border-radius: 5px;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 10px 15px;
            text-decoration: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <h4>CHỈNH SỬA THÚ CƯNG</h4>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Tên thú cưng</label>
            <input name="name" type="text" id="name" value="<?= htmlspecialchars($pet['name']) ?>" />
        </div>
        <div class="form-group">
            <label for="price">Giá</label>
            <input name="price" type="number" step="0.01" id="price" value="<?= htmlspecialchars($pet['price']) ?>" />
        </div>
        <div class="form-group">
            <label for="priceSale">Giá khuyến mãi</label>
            <input name="priceSale" type="number" step="0.01" id="priceSale" value="<?= htmlspecialchars($pet['priceSale']) ?>" />
        </div>
        <div class="form-group">
            <label>Giới tính:</label>
            <input name="gender" type="radio" value="1" <?= $pet['gender'] == 1 ? 'checked' : '' ?> /> Đực
            <input name="gender" type="radio" value="0" <?= $pet['gender'] == 0 ? 'checked' : '' ?> /> Cái
        </div>
        <div class="form-group">
            <label for="quantity">Số lượng</label>
            <input name="quantity" type="number" id="quantity" value="<?= htmlspecialchars($pet['quantity']) ?>" />
        </div>

        <div class="form-group">
        <div class="form-group">
            <label for="urlImg">Hình ảnh hiện tại:</label>
            <img src="<?= htmlspecialchars('../' . $pet['urlImg']) ?>" alt="Hình ảnh thú cưng" style="max-width: 200px; height: auto; border-radius: 5px;"/>
            <label for="urlImgNew">Chọn hình ảnh mới:</label>
            <input name="urlImgNew" type="file" id="urlImgNew" accept="image/*" />
        </div>




        <div class="form-group">
            <label for="idLoai">Loại thú cưng</label>
            <input name="idLoai" type="text" id="idLoai" value="<?= htmlspecialchars($pet['idLoai']) ?>" />
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea name="description" id="description"><?= htmlspecialchars($pet['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="hot">Hot</label>
            <input name="hot" type="radio" value="1" <?= $pet['hot'] == 1 ? 'checked' : '' ?> /> Có
            <input name="hot" type="radio" value="0" <?= $pet['hot'] == 0 ? 'checked' : '' ?> /> Không
        </div>
        <div class="form-group btn-group">
            <input name="btn" type="submit" value="Lưu thông tin" class="btn btn-primary" />
            <a href="index.php?page=pets_ds" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</body>
</html>
