function checkCartItems() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', '../config/check_cart.php', true); // Đảm bảo đường dẫn đúng
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.totalQuantity >= 0) {
                    displayCartIcon(response.totalQuantity);
                }
            } catch (error) {
                console.error('Response không phải là JSON hợp lệ:', xhr.responseText);
                // Xử lý lỗi ở đây, ví dụ hiển thị thông báo lỗi cho người dùng
            }
        } else {
            console.error('Lỗi HTTP:', xhr.status, xhr.statusText);
            // Xử lý lỗi HTTP ở đây
        }
    };
    xhr.send();
}

function displayCartIcon(quantity) {
    let cartIcon = document.querySelector('.cart-count');
    cartIcon.textContent = quantity;
}

document.addEventListener("DOMContentLoaded", function() {
    checkCartItems();
});


// Gọi hàm này sau khi thêm hoặc bớt sản phẩm