
<?php
$thongbao = "";

if (isset($_POST['btn1'])) {

    $email = trim(strip_tags($_POST['email'])); // Tiếp nhận email

    // Kiểm tra định dạng email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $thongbao .= "Email không đúng <br>"; 
    } else {
        // Kiểm tra email có phải là thành viên không
        require_once 'connectdb.php';

        $sql = "SELECT count(*) FROM users WHERE email = '{$email}'";
        $kq = $conn->query($sql);
        $row = $kq->fetch();

        if ($row[0] == 0) {
            $thongbao .= "Email này không phải là thành viên <br>";
        } else {
            // Phát sinh mật khẩu ngẫu nhiên, mã hóa MD5 và lấy 8 ký tự đầu
            $pass_moi = md5(random_int(0, 9999));
            $pass_moi = substr($pass_moi, 0, 8);

            // Cập nhật mật khẩu mới vào bảng users
            $sql_update = "UPDATE users SET pass='{$pass_moi}' WHERE email='{$email}'";
            $kq_update = $conn->query($sql_update);

            if ($kq_update) {
                $thongbao .= "Cập nhật mật khẩu thành công<br>";

                // Gửi mail dùng PHPMailer
                require_once "PHPMailer-master/src/PHPMailer.php";
                require_once "PHPMailer-master/src/Exception.php";
                // require_once "PHPMailer-master/src/OAuth.php";
                require_once "PHPMailer-master/src/SMTP.php";

                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    // Debug mode
                    $mail->SMTPDebug = 2; // Đặt 0 để tắt debug sau khi test
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Server gửi thư
                    $mail->SMTPAuth = true;
                    $mail->Username = '2251120353@ut.edu.vn'; // Địa chỉ Gmail của bạn
                    $mail->Password = 'huylinh2k'; // Mật khẩu Gmail của bạn
                    $mail->SMTPSecure = 'TLS'; // Hoặc TLS
                    $mail->Port = 587; // Hoặc 587
                    $mail->CharSet = "UTF-8";

                    // Cấu hình SSL
                    $mail->smtpConnect([
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false,
                            "allow_self_signed" => true
                        ]
                    ]);

                    // Khai báo người gửi và người nhận mail
                    $mail->setFrom('diachi@gmail.com', 'Ban quản trị website');
                    $mail->addAddress($email, 'Quý khách');

                    // Nội dung email
                    $mail->isHTML(true);
                    $mail->Subject = 'Cấp lại mật khẩu mới';
                    $mail->Body = "Đây là mật khẩu mới của bạn: <b>{$pass_moi}</b>";

                    // Gửi email
                    $mail->send();
                    $thongbao .= "Đã gửi mail thành công<br>";
                } catch (Exception $e) {
                    $thongbao .= "Lỗi khi gửi thư: " . $mail->ErrorInfo . "<br>";
                }

            } else {
                $thongbao .= "Cập nhật mật khẩu không thành công<br>";
            }
        }
    }
}
?>