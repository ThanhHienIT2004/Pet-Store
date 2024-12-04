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
## 📷 Hình ảnh giao diện  

### 1. Trang chủ  
![image](https://github.com/user-attachments/assets/953dfb7c-0b90-4829-892c-f7005e762da5)
![image](https://github.com/user-attachments/assets/96459709-a43d-44cd-929c-e570693b9742)
![image](https://github.com/user-attachments/assets/2f3b0dbb-d654-4148-abf4-f087ff3992d1)
![image](https://github.com/user-attachments/assets/51737525-ab4c-43d7-8064-b8c8cd4ea127)
![image](https://github.com/user-attachments/assets/2cbac419-ecbc-4bbb-a8f9-f12cc4a0a8c7)
![image](https://github.com/user-attachments/assets/2b91e2e0-84f9-42b4-8502-e685bf0b124f)




*Giao diện chính với danh sách thú cưng nổi bật.*

### 2. Trang danh mục sản phẩm  
![image](https://github.com/user-attachments/assets/f4558930-d0d9-4399-8760-d812468b0503)
*Danh sách thú cưng theo danh mục (mèo, chó, vẹt).*

### 3. Trang chi tiết sản phẩm  
![image](https://github.com/user-attachments/assets/e271f503-3e09-49b2-a5dc-5277423626bc)
*Hiển thị thông tin chi tiết về thú cưng (giống, giá, mô tả).*

### 4. Giỏ hàng  
![image](https://github.com/user-attachments/assets/33a97032-52db-4a61-9301-ff5ab89ed453)
*Trang giỏ hàng, khi không có sản phẩm nào trong giỏ hàng.*
![image](https://github.com/user-attachments/assets/c1a80308-efde-461f-afe8-08559383aede)
*Trang giỏ hàng, hiển thị danh sách thú cưng đã thêm cùng tổng giá trị.*

### 5. Trang đơn đặt hàng
![image](https://github.com/user-attachments/assets/894d2565-a56e-45fe-bd47-5af5fb1521cb)
*Trang đơn hàng, hiển thị đơn đặt hàng.*

### 6. Trang thông tin người dùng 
![image](https://github.com/user-attachments/assets/733419ce-8f0f-427d-b48a-bc4a066785a9)

### 7. Trang admin
![image](https://github.com/user-attachments/assets/037bd8de-9f2f-4be0-a0b6-8fa3db83a2c7)
![image](https://github.com/user-attachments/assets/dd39cc8c-87a8-485a-960f-c385ecc316ff)
![image](https://github.com/user-attachments/assets/6f41a560-383d-4b83-bba9-959e313940f4)
![image](https://github.com/user-attachments/assets/8df8fca4-969f-41a3-95b6-1b7b29840926)
![image](https://github.com/user-attachments/assets/500eccf2-fb83-443f-bd0a-44943db226da)
*Trang admin có thể xem danh sách thú cưng, thêm, xóa thú cưng, quản lí người dùng, xem danh sách đơn hàng*
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

