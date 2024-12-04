document.addEventListener('DOMContentLoaded', function () {
    setupDetailButtons();
    setupModalClose();
});

function setupDetailButtons() {
    // Lấy tất cả các nút 'Chi tiết'
    const detailButtons = document.querySelectorAll('.view-details');

    detailButtons.forEach(button => {
        button.addEventListener('click', function () {
            const petId = this.dataset.id;
            console.log('Clicked button for pet ID:', petId); // Debug log

            // Gọi hàm openDetailModal để mở modal và hiển thị chi tiết
            openDetailModal(petId);
        });
    });
}

function setupModalClose() {
    // Đóng modal khi nhấp vào nút đóng
    const closeButton = document.querySelector('#modal .custom-close');
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    // Đóng modal khi nhấp vào vùng ngoài modal
    window.addEventListener('click', function (event) {
        if (event.target == document.querySelector('#modal')) {
            closeModal();
        }
    });
}

function openDetailModal(petId) {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `DetailPet.php?action=getPetDetails&id=${petId}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                if (data.error) {
                    alert(data.error);
                } else {
                    updateModalContent(data);
                    document.querySelector('#modal').style.display = 'flex';
                }
            } catch (error) {
                console.error('Error parsing response:', error);
                alert('Lỗi khi xử lý dữ liệu từ server.');
            }
        } else {
            console.error('Request failed. Status:', xhr.status);
            alert('Lỗi khi gửi yêu cầu.');
        }
    };
    xhr.onerror = function() {
        console.error('Network error occurred');
        alert('Có lỗi xảy ra khi gửi yêu cầu.');
    };
    xhr.send();
}

function updateModalContent(data) {
    document.querySelector('#modal .custom-modal-title').innerText = data.name;
    document.querySelector('#modal .custom-modal-img').src = data.urlImg;
    document.querySelector('#modal .custom-modal-price').innerText = `Giá: ${data.price.toLocaleString()}đ`;
    document.querySelector('#modal .custom-modal-sale-price').innerText = `Giá khuyến mãi: ${data.priceSale.toLocaleString()}đ`;
    document.querySelector('#modal .custom-modal-quantity').innerText = `Số lượng còn lại: ${data.quantity}`;
    document.querySelector('#modal .custom-modal-description').innerText = `Mô tả: ${data.description}`;
    
    // Thêm dòng này để cập nhật ID cho nút "Đặt hàng"
    document.querySelector('#modal .add-to-cart').setAttribute('data-pet-id', data.id);
}

function closeModal() {
    document.querySelector('#modal').style.display = 'none';
}

// Thêm hàm này để cập nhật các nút chi tiết sau khi tìm kiếm
function updateDetailButtons() {
    setupDetailButtons();
}

// function goToCart() {
//     location.href = "index.php?page=cart";
//     location.href.reload();
// }
