<?php
$thongbao = array('success' => false, 'message' => '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim(strip_tags($_POST['email'])); // Tiếp nhận email

    // Kiểm tra định dạng email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $thongbao['message'] = 'Email không đúng';
    } else {
        require 'config.php'; // Đảm bảo config.php có kết nối CSDL

        $sql = "SELECT count(*) FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $row = $stmt->fetchColumn();

        if ($row == 0) {
            $thongbao['message'] = 'Email này không phải là thành viên';
        } else {
            // Tạo mật khẩu mới
            $pass_moi = md5(random_int(0, 9999));
            $pass_moi_hashed = password_hash($pass_moi, PASSWORD_DEFAULT);

            $sql_update = "UPDATE users SET pass = :pass WHERE email = :email";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':pass', $pass_moi_hashed);
            $stmt_update->bindParam(':email', $email);
            $stmt_update->execute();

            if ($stmt_update->rowCount() > 0) {
                $thongbao['success'] = true;
                $thongbao['message'] = 'Cập nhật mật khẩu thành công';

                // Gửi email với mật khẩu mới
                require_once "../PHPMailer-master/src/PHPMailer.php";
                require_once "../PHPMailer-master/src/Exception.php";
                require_once "../PHPMailer-master/src/SMTP.php";

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = '2251120353@ut.edu.vn';
                    $mail->Password = 'huylinh2k';
                    $mail->SMTPSecure = 'tls'; // Đổi thành SSL cho port 465 nếu cần
                    $mail->Port = 587;
                    $mail->CharSet = "UTF-8";

                    $mail->setFrom('diachi@gmail.com', 'Ban quản trị website');
                    $mail->addAddress($email, 'Quý khách');

                    $mail->isHTML(true);
                    $mail->Subject = 'Cấp lại mật khẩu mới';
                    $mail->Body = "Đây là mật khẩu mới của bạn: <b>{$pass_moi}</b>";

                    $mail->send();
                } catch (Exception $e) {
                    $thongbao['message'] = 'Lỗi khi gửi thư: ' . $mail->ErrorInfo;
                }
            } else {
                $thongbao['message'] = 'Cập nhật mật khẩu không thành công';
            }
        }
    }
}

header('Content-Type: application/json');
echo json_encode($thongbao);
?>
