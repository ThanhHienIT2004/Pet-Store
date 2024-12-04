# 🐾 Pet-Store 🐾  
**Pet-Store** là một website thương mại điện tử chuyên bán các loại thú cưng như mèo, chó, vẹt. Dự án được xây dựng nhằm giúp người dùng dễ dàng tìm kiếm, chọn mua thú cưng yêu thích, và cung cấp thông tin chi tiết về từng loài.


---

## 🌟 Tính năng chính 
### Web được thiết kế với 3 loại người dùng: 
- Khách vãn lai (không đăng nhập).
- Khách đã có tài khoản và đã đăng nhập.
- Admin quản lý sản phẩm.

- **Trang chủ**: Giới thiệu tổng quan về cửa hàng và các thú cưng nổi bật.  
- **Danh mục sản phẩm**: Danh sách các loại thú cưng (mèo, chó, vẹt) với hình ảnh, giá cả và mô tả chi tiết.  
- **Chi tiết sản phẩm**: Hiển thị thông tin đầy đủ về thú cưng, bao gồm giống, tuổi, cân nặng, v.v.  
- **Giỏ hàng**: Người dùng có thể thêm/xóa thú cưng vào giỏ hàng và kiểm tra tổng giá trị.  
- **Tìm kiếm**: Hỗ trợ tìm kiếm theo tên hoặc loại thú cưng.  
- **Quản lý admin**: Thêm, chỉnh sửa, và xóa sản phẩm.

---

## 🛠️ Công nghệ sử dụng  
- **Front-end**:  
  - HTML  
  - CSS  
  - JavaScript  
- **Back-end**:  
  - PHP  
- **Cơ sở dữ liệu**:  
  - MySQL  

---

## 🖥️ Cách cài đặt và chạy dự án  

### Yêu cầu:  
- **XAMPP** hoặc máy chủ web bất kỳ hỗ trợ PHP và MySQL.  

---

### Hướng dẫn từng bước:  

1. **Tải và cài đặt XAMPP**:  
   - Tải xuống XAMPP từ [Apache Friends](https://www.apachefriends.org/).  
   - Cài đặt và đảm bảo bạn đã bật **Apache** và **MySQL** trong **XAMPP Control Panel**.  

2. **Clone hoặc tải về dự án**:  
   - Clone dự án từ GitHub:  
     ```bash  
     git clone https://github.com/<your-username>/pet-store.git  
     ```  
   - Hoặc tải file ZIP, sau đó giải nén vào thư mục mong muốn.  

3. **Copy dự án vào thư mục XAMPP**:  
   - Copy toàn bộ thư mục dự án (`pet-store`) vào thư mục:  
     ```
     C:/xampp/htdocs/
     ```  

4. **Cài đặt cơ sở dữ liệu**:  
   - Mở **phpMyAdmin** bằng cách truy cập:  
     ```
     http://localhost/phpmyadmin/
     ```  
   - Tạo một database mới, ví dụ: `pet_store`.  
   - Import file cơ sở dữ liệu đi kèm:  
     - Nhấp vào tab **Import**.  
     - Chọn file `database/pet_store.sql`.  
     - Nhấp **Go** để hoàn thành quá trình import.  

5. **Kiểm tra và chỉnh sửa file kết nối cơ sở dữ liệu (nếu cần)**:  
   - Mở file `config.php` trong thư mục dự án.  
   - Đảm bảo thông tin kết nối đúng:  
     ```php  
     $servername = "localhost";  
     $username = "root";  
     $password = "";  // Mặc định trống với XAMPP  
     $dbname = "pet_store";  
     ```  

6. **Khởi chạy dự án**:  
   - Mở trình duyệt và nhập địa chỉ:  
     ```
     http://localhost/pet-store/
     ```  
   - Website **Pet-Store** sẽ được hiển thị!  

---

### Xử lý lỗi phổ biến:  
1. **Apache hoặc MySQL không chạy được**:  
   - Kiểm tra xem có phần mềm nào xung đột cổng (như Skype, Teams) không.  
   - Đổi cổng Apache trong **XAMPP Control Panel > Config > Apache (httpd.conf)**, sửa:  
     ```
     Listen 80  
     ```  
     thành:  
     ```
     Listen 8080  
     ```  
     Sau đó truy cập:  
     ```
     http://localhost:8080/pet-store/
     ```  

2. **Không thể kết nối cơ sở dữ liệu**:  
   - Đảm bảo đã tạo đúng tên database (`pet_store`).  
   - Kiểm tra thông tin trong `config.php` (username, password).  

3. **Trang trắng hoặc báo lỗi PHP**:  
   - Bật chế độ hiển thị lỗi bằng cách thêm dòng này vào đầu file `index.php`:  
     ```php  
     ini_set('display_errors', 1);  
     error_reporting(E_ALL);  
     ```  

---

Làm theo các bước trên, bạn sẽ mở được dự án **Pet-Store** thành công! Nếu gặp bất kỳ vấn đề gì, hãy để lại phản hồi. 😊  

