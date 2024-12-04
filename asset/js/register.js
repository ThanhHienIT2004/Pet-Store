document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');

    const registerForm = document.getElementById('registerForm');
    const usernameInput = document.getElementById('register-username');
    const emailInput = document.getElementById('register-email');
    const passwordInput = document.getElementById('register-password');
    const confirmPasswordInput = document.getElementById('register-confirmPassword');
    
    const usernameError = document.createElement('div');
    const emailError = document.createElement('div');
    const passwordError = document.createElement('div');
    const confirmPasswordError = document.createElement('div');

    usernameError.style.color = 'red';
    emailError.style.color = 'red';
    passwordError.style.color = 'red';
    confirmPasswordError.style.color = 'red';

    usernameInput.parentNode.insertBefore(usernameError, usernameInput.nextSibling);
    emailInput.parentNode.insertBefore(emailError, emailInput.nextSibling);
    passwordInput.parentNode.insertBefore(passwordError, passwordInput.nextSibling);
    confirmPasswordInput.parentNode.insertBefore(confirmPasswordError, confirmPasswordInput.nextSibling);

    // Xử lý khi gửi form đăng ký
    registerForm.addEventListener('submit', function(event) {
        event.preventDefault();

        // Xóa thông báo lỗi trước đó
        usernameError.innerHTML = '';
        emailError.innerHTML = '';
        passwordError.innerHTML = '';
        confirmPasswordError.innerHTML = '';

        const formData = new FormData(registerForm);
        formData.append('register', 'true'); // Thêm trường register

        fetch('../pages/register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // In dữ liệu phản hồi ra console để kiểm tra

            if (data.success) {
                // Hiển thị thông báo thành công dạng alert
                alert('Đăng ký thành công!');

                // Đóng modal đăng ký và mở modal đăng nhập khi thành công
                closeRegisterModal();
                openLoginModal();
            } else {
                // Hiển thị thông báo lỗi cụ thể
                if (data.errors) {
                    if (data.errors.username) {
                        usernameError.innerHTML = data.errors.username;
                    }
                    if (data.errors.email) {
                        emailError.innerHTML = data.errors.email;
                    }
                    if (data.errors.confirmPassword) {
                        confirmPasswordError.innerHTML = data.errors.confirmPassword;
                    }
                } else {
                    console.error('Đăng ký thất bại.');
                }
            }
        })
        .catch(error => {
            console.error('Đã xảy ra lỗi khi gửi yêu cầu.', error);
        });
    });

    window.openRegisterModal = function() {
        document.getElementById('registerModal').style.display = 'block';
    };

    window.closeRegisterModal = function() {
        document.getElementById('registerModal').style.display = 'none';
    };

    window.openLoginModal = function() {
        document.getElementById('loginModal').style.display = 'block';
    };

    window.closeLoginModal = function() {
        document.getElementById('loginModal').style.display = 'none';
    };

    // Gắn sự kiện lắng nghe cho nút đóng modal đăng ký
    if (closeRegisterModalButton) {
        closeRegisterModalButton.addEventListener('click', function() {
            closeRegisterModal();
            closeLoginModal(); // Đóng cả hai modal khi nhấn nút X trong modal đăng ký
        });
    }

    // Xử lý nút quay lại từ modal đăng ký
    if (backToLogin) {
        backToLogin.onclick = function() {
            closeRegisterModal();
            openLoginModal();
        };
    }
});
