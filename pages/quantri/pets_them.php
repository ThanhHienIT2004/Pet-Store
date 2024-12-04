<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>THÊM THÚ CƯNG</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f4f8;
            color: #333;
            line-height: 1.6;
        }

        h4 {
            text-align: center;
            margin: 30px 0;
            color: #2c3e50;
            font-size: 28px;
            text-transform: uppercase;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #34495e;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #bdc3c7;
            border-radius: 6px;
            transition: border-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group input[type="file"]:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-group input[type="radio"] {
            margin-right: 10px;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2ecc71;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #27ae60;
        }
    </style>
</head>
<body>
    <h4>THÊM THÚ CƯNG</h4>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>ID Thú Cưng</label>
            <input name="id" type="text" required />
        </div>
        <div class="form-group">
            <label>Tên thú cưng</label>
            <input name="name" type="text" required />
        </div>
        <div class="form-group">
            <label>Giá</label>
            <input name="price" type="number" step="0.01" required />
        </div>
        <div class="form-group">
            <label>Giá khuyến mãi</label>
            <input name="priceSale" type="number" step="0.01" />
        </div>
        <div class="form-group">
            <label>Giới tính:</label>
            <input name="gender" type="radio" value="1" required /> Đực
            <input name="gender" type="radio" value="0" /> Cái
        </div>
        <div class="form-group">
            <label>Số lượng</label>
            <input name="quantity" type="number" required />
        </div>
        <div class="form-group">
            <label for="imageUpload">Chọn hình ảnh</label>
            <input name="urlImg" type="file" id="imageUpload" accept="image/*" required />
        </div>

        <div class="form-group">
            <label>Danh mục</label>
            <input name="idLoai" type="text" required />
        </div>
        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description"></textarea>
        </div>
        <div class="form-group">
            <label>Nổi bật:</label>
            <input name="hot" type="radio" value="1" /> Có
            <input name="hot" type="radio" value="0" checked /> Không
        </div>
        <div class="form-group" style="text-align: center;">
            <input name="btn" type="submit" value="Thêm" class="btn" />
        </div>
    </form>

    <?php
    if (isset($_POST['btn'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $priceSale = $_POST['priceSale'];
        $gender = $_POST['gender'];
        $quantity = $_POST['quantity'];
        $idLoai = $_POST['idLoai'];
        $description = $_POST['description'];
        $hot = $_POST['hot'];

        $uploadDir = '../../asset/uploads/'; // Đường dẫn tương đối đến thư mục uploads

        // Handle file upload
        if (isset($_FILES['urlImg']) && $_FILES['urlImg']['error'] == 0) {
            $fileTmpPath = $_FILES['urlImg']['tmp_name'];
            $fileName = basename($_FILES['urlImg']['name']);
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Kiểm tra loại tệp (chỉ cho phép hình ảnh)
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileExtension, $allowedExts)) {
                $newFileName = uniqid() . '.' . $fileExtension; // Tạo tên file mới để tránh trùng lặp
                $dest_path = $uploadDir . $newFileName;

                // Di chuyển tệp từ thư mục tạm thời đến thư mục uploads
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $urlImg = '../asset/uploads/' . $newFileName; // Cập nhật đường dẫn mới
                } else {
                    echo 'Có lỗi xảy ra khi tải lên hình ảnh mới.';
                    exit();
                }
            } else {
                echo 'Loại tệp không được phép. Chỉ cho phép hình ảnh.';
                exit();
            }
        } else {
            echo 'No file uploaded or there was an error uploading the file.';
            exit();
        }

        // Xử lý dữ liệu đầu vào
        $id = trim(strip_tags($id));
        $name = trim(strip_tags($name));
        settype($price, "float");
        settype($priceSale, "float");
        settype($gender, "int");
        settype($quantity, "int");
        settype($hot, "int");
        $idLoai = trim(strip_tags($idLoai));
        $description = trim(strip_tags($description));

        // Gọi hàm thêm thú cưng
        require_once 'functions.php';
        $kq = themPets($id, $name, $price, $priceSale, $gender, $quantity, $urlImg, $idLoai, $description, $hot);

    
    }
    ?>
</body>
</html>
