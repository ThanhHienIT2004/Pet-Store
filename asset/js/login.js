document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorMessage = document.getElementById('error-message');

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('../config/check-login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Lưu thông báo vào localStorage
                    localStorage.setItem('loginMessage', data.message);
                    // Tải lại trang
                    location.reload();
                } else {
                    // Hiển thị thông báo lỗi
                    errorMessage.textContent = data.error;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = "Đã xảy ra lỗi khi gửi yêu cầu.";
            });
        });
    }

    // Kiểm tra và hiển thị thông báo đăng nhập thành công sau khi trang đã tải
    const loginMessage = localStorage.getItem('loginMessage');
    if (loginMessage) {
        showSuccessPopup(loginMessage);
        localStorage.removeItem('loginMessage'); // Xóa thông báo sau khi đã hiển thị
    }

    // Hàm hiển thị popup thành công
    function showSuccessPopup(message) {
        const popup = document.createElement('div');
        popup.className = 'success-popup';
        popup.textContent = message;
        document.body.appendChild(popup);

        // Tự động đóng popup sau 3 giây
        setTimeout(() => {
            popup.remove();
        }, 1000);
    }

    // Hàm cập nhật UI sau khi đăng nhập
    function updateUIAfterLogin() {
        // Ví dụ: Ẩn nút đăng nhập, hiển thị nút đăng xuất
        const loginButton = document.getElementById('loginButton');
        const logoutButton = document.getElementById('logoutButton');
        if (loginButton) loginButton.style.display = 'none';
        if (logoutButton) logoutButton.style.display = 'block';

        // Cập nhật các phần khác của UI nếu cần
    }

    // Hàm đóng modal đăng nhập (nếu bạn sử dụng modal)
    function closeLoginModal() {
        const loginModal = document.getElementById('loginModal');
        if (loginModal) loginModal.style.display = 'none';
    }
});
