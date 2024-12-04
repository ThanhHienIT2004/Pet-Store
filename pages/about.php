<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Giới thiệu - Cửa hàng thú cưng</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    /* CSS cho trang about.html */
   .header {
      color: white;
      padding: 20px;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 36px;
    }

    .content {
      max-width: 1200px;
      margin: 20px auto;
      padding: 20px;
      background-color: white;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .content h2 {
      font-size: 28px;
      margin-bottom: 20px;
    }

    .content p {
      font-size: 18px;
      line-height: 1.6;
      margin-bottom: 20px;
      color: black;
    }

    .content img {
      width: 100%;
      max-width: 600px;
      display: block;
      margin: 20px auto;
      border-radius: 10px;
    }

    .team-section {
      text-align: center;
    }

    .team-section h2 {
      font-size: 28px;
      margin-bottom: 20px;
      color: #333;
    }

    .team-members {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
    }

    .team-member {
      max-width: 250px;
      margin: 10px;
      text-align: center;
    }

    .team-member img {
      width: 100%;
      border-radius: 50%;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .team-member h3 {
      margin-top: 10px;
      font-size: 20px;
    }

    .footer {

      color: white;
      text-align: center;
      padding: 10px;
      margin-top: 20px;
    }

    .footer p {
      margin: 0;
    }
  </style>
</head>
<body>

  <div class="header">
    <h1>Chào mừng đến với Cửa hàng Thú Cưng</h1>
  </div>

  <div class="content">
    <h2>Giới thiệu về chúng tôi</h2>
    <p>Chúng tôi là cửa hàng chuyên cung cấp các loài thú cưng từ chó, mèo, đến các loài chim, cá và bò sát đáng yêu. Với niềm đam mê và tình yêu dành cho động vật, cửa hàng chúng tôi cam kết mang đến những người bạn nhỏ khoẻ mạnh, đáng yêu nhất đến với gia đình của bạn.</p>

    <h2>Sứ mệnh của chúng tôi</h2>
    <p>Sứ mệnh của chúng tôi là tạo ra một môi trường thân thiện, nơi mọi người có thể tìm thấy người bạn đồng hành hoàn hảo. Chúng tôi không chỉ bán thú cưng mà còn cung cấp các dịch vụ chăm sóc, dinh dưỡng và tư vấn sức khỏe nhằm đảm bảo thú cưng của bạn luôn khoẻ mạnh và hạnh phúc.</p>
    
    <div class="team-section">
      <h2>Đội ngũ của chúng tôi</h2>
      <div class="team-members">
        <div class="team-member">
          <img src="../asset/images/cat/head.png" alt="Team Member 1">
          <h3>Trần Nguyễn Thành Hiển</h3>
          <p>Chuyên gia chăm sóc thú cưng</p>
        </div>
        <div class="team-member">
          <img src="../asset/images/dog/head.png" alt="Team Member 2">
          <h3>Nguyễn Hoàng Huy</h3>
          <p>Bác sĩ thú y</p>
        </div>
        <div class="team-member">
          <img src="../asset/images/parrot/head.png" alt="Team Member 3">
          <h3>Phạm Đăng Quang</h3>
          <p>Nhân viên tư vấn</p>
        </div>
      </div>
    </div>
  </div>

  <div class="footer">
    <p>&copy; 2024 Cửa hàng Thú Cưng. All rights reserved.</p>
  </div>

</body>
</html>
