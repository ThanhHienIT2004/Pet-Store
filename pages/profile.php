<?php
require '../config/config.php'; // Ensure database connection

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get the username from session
$username = $_SESSION['username'];

// Retrieve user information from the database
try {
    $sql = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if (!$user) {
        echo "User does not exist.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle user information update
$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $fullname = $_POST['fullname'];
    $email = !empty($_POST['email']) ? $_POST['email'] : NULL;  // Cho phép email null
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['pass'];

    try {
        $sql = "UPDATE users SET phone = :phone, fullname = :fullname, email = :email, address = :address, dob = :dob, pass = :pass WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);  // Sử dụng PDO::PARAM_STR để chấp nhận NULL
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':pass', $password);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $message = "Thông tin đã được cập nhật thành công.";
        echo '<script type="text/javascript">
                    window.location.href = "index.php?page=index_user&pageuser=profile";
              </script>';
    } catch (PDOException $e) {
        $message = "Lỗi cập nhật thông tin: " . $e->getMessage();
    }
}

?>
<link rel="stylesheet" href="../asset/css/profile.css">
<body>
    <div class="tt">
        <div class="content">
            <h2>Thông Tin Tài Khoản</h2>
            <form id="profileForm" class="profile" action="" method="post">
    <label for="username">Tên đăng nhập:</label>
    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
    
    <label for="fullname">Họ và tên:</label>
    <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
    
    <label for="phone">Số điện thoại:</label>
    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required pattern="0[0-9]{9,10}">
    
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
    
    <label for="address">Địa chỉ:</label>
    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
    
    <label for="dob">Ngày sinh:</label>
    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">

    <button type="submit">Cập nhật thông tin</button>
    <?php if ($message) { echo '<p style="color:red;">' . htmlspecialchars($message) . '</p>'; } ?>
</form>

        </div>
    </div>
</body>
