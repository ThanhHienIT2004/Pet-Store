document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const errorMessage = document.getElementById('error-message');
    const closeLoginModalButton = document.getElementById('closeLoginModalButton');
    const closeForgotPasswordModalButton = document.getElementById('closeForgotPasswordModalButton');
    const closeRegisterModalButton = document.getElementById('closeRegisterModalButton');
    const backToLogin = document.getElementById('backToLogin');

    // Xử lý form đăng nhập
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Ngăn form gửi dữ liệu theo cách mặc định

        const formData = new FormData(loginForm);

        fetch('../pages/login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeLoginModal(); // Đóng modal đăng nhập
                location.reload(); // Tải lại trang để cập nhật trạng thái đăng nhập
            } else {
                errorMessage.textContent = data.error; // Sử dụng textContent thay vì innerHTML
            }
        })
        .catch(() => {
            errorMessage.textContent = "Đã xảy ra lỗi khi gửi yêu cầu."; // Sử dụng textContent thay vì innerHTML
        });
    });

    // Xử lý form quên mật khẩu
    forgotPasswordForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Ngăn form gửi dữ liệu theo cách mặc định

        const formData = new FormData(forgotPasswordForm);

        fetch('quenpass.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                closeForgotPasswordModal(); // Đóng modal quên mật khẩu
                openLoginModal(); // Mở modal đăng nhập
            } else {
                alert('Có lỗi xảy ra, vui lòng thử lại.');
            }
        })
        .catch(() => {
            alert('Có lỗi xảy ra khi gửi yêu cầu.');
        });
    });

    // Mở modal quên mật khẩu
    window.openForgotPasswordModal = function() {
        document.getElementById('forgotPasswordModal').style.display = 'block';
    };

    // Đóng modal quên mật khẩu
    window.closeForgotPasswordModal = function() {
        document.getElementById('forgotPasswordModal').style.display = 'none';
    };

    // Mở modal đăng nhập
    window.openLoginModal = function() {
        document.getElementById('loginModal').style.display = 'block';
    };

    // Đóng modal đăng nhập
    window.closeLoginModal = function() {
        document.getElementById('loginModal').style.display = 'none';
    };

    // Mở modal đăng ký
    window.openRegisterModal = function() {
        document.getElementById('registerModal').style.display = 'block';
    };

    // Đóng modal đăng ký
    window.closeRegisterModal = function() {
        document.getElementById('registerModal').style.display = 'none';
    };

    // Gắn sự kiện lắng nghe cho nút đóng modal đăng nhập
    if (closeLoginModalButton) {
        closeLoginModalButton.addEventListener('click', closeLoginModal);
    }

    // Gắn sự kiện lắng nghe cho nút đóng modal quên mật khẩu
    if (closeForgotPasswordModalButton) {
        closeForgotPasswordModalButton.addEventListener('click', closeForgotPasswordModal);
    }

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

    // Đóng modal khi nhấn ra ngoài vùng modal
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };
});
