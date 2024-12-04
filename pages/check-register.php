<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pet-store";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nhận dữ liệu từ yêu cầu
$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'];

// Truy vấn cơ sở dữ liệu để kiểm tra tên người dùng
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$response = array();
if ($result->num_rows > 0) {
    // Tên người dùng đã tồn tại
    $response['usernameTaken'] = true;
} else {
    // Tên người dùng chưa tồn tại
    $response['usernameTaken'] = false;
}

// Đóng kết nối
$stmt->close();
$conn->close();

// Trả về kết quả dưới dạng JSON
header('Content-Type: application/json');
echo json_encode($response);
?>