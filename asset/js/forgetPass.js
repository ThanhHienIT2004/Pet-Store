document.addEventListener('DOMContentLoaded', function() {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');

    forgotPasswordForm.addEventListener('submit', function(event) {
        event.preventDefault(); // Ngăn form gửi dữ liệu theo cách mặc định

        const formData = new FormData(forgotPasswordForm);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../config/forget_pass.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Chuyển đổi FormData thành chuỗi URL-encoded
        const urlEncodedData = new URLSearchParams(formData).toString();

        xhr.send(urlEncodedData);

        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        closeForgotPasswordModal(); // Đóng modal quên mật khẩu
                        displayError('Đã gửi mật khẩu mới'); // Hiển thị thông báo thành công
                    } else {
                        displayError(response.message); // Hiển thị thông báo lỗi từ server
                    }
                } catch (error) {
                    displayError('Lỗi khi xử lý dữ liệu từ server.');
                }
            } else {
                displayError('Lỗi khi gửi yêu cầu. Status code: ' + xhr.status);
            }
        };

        xhr.onerror = function() {
            displayError('Có lỗi xảy ra khi gửi yêu cầu.');
        };
    });

    // Add this line to use the function
    window.openForgotPasswordModal = function() {
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        forgotPasswordModal.style.display = 'block';
    }

    function closeForgotPasswordModal() {
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        forgotPasswordModal.style.display = 'none';
    }

    // Thêm đoạn này vào đầu file hoặc trong DOMContentLoaded event listener
    const style = document.createElement('style');
    style.textContent = `
        .forget-pass-popup {
            display: none;
            position: fixed;
            top: 10%;
            right: 20px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000111;
        }
        .forget-pass-popup-message {
            color: #333;
            font-size: 14px;
        }
    `;
    document.head.appendChild(style);

    // Hàm displayError cập nhật
    function displayError(message) {
        let popup = document.querySelector('.forget-pass-popup');
        if (!popup) {
            popup = document.createElement('div');
            popup.className = 'forget-pass-popup';
            const popupMessage = document.createElement('span');
            popupMessage.className = 'forget-pass-popup-message';
            popup.appendChild(popupMessage);
            document.body.appendChild(popup);
        }
        const popupMessage = popup.querySelector('.forget-pass-popup-message');
        popupMessage.textContent = message;
        popup.style.display = 'block';
        setTimeout(function() {
            popup.style.display = 'none';
        }, 2000);
    }

    document.getElementById('closeForgotPasswordModalButton').addEventListener('click', function() {
        closeForgotPasswordModal();
    });
});
