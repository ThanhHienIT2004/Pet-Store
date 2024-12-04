// Hàm thêm sản phẩm vào giỏ hàng
function addToPet(petId) {
    sendCartRequest('add', petId);
}

// Hàm xóa sản phẩm khỏi giỏ hàng
function removeFromCart(petId) {
    showConfirmModal('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?', function() {
        sendCartRequest('remove', petId);
    });
}
// Hàm gửi yêu cầu đến server
function sendCartRequest(action, petId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../config/cart_config.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                if (action === 'remove') {
                    // Lưu thông tin popup vào localStorage
                    localStorage.setItem('popupMessage', response.message);
                    localStorage.setItem('popupType', 'info');
                    
                    // Tải lại trang
                    location.reload();
                } else {
                    showPopup(response.message, 'success');
                    updateCartDisplay(response.cartCount);
                }
            } else {
                showPopup("Có lỗi xảy ra: " + response.message, "error");
            }
            
            // Log thông tin session
            console.log("Session Info:", response.session_info);
        }
    };

    xhr.send("action=" + action + "&pet_id=" + encodeURIComponent(petId));
}

// Thêm hàm này để kiểm tra và hiển thị popup sau khi trang đã tải
function checkAndShowPopup() {
    var message = localStorage.getItem('popupMessage');
    var type = localStorage.getItem('popupType');
    if (message) {
        showPopup(message, type);
        // Xóa thông tin popup từ localStorage sau khi đã hiển thị
        localStorage.removeItem('popupMessage');
        localStorage.removeItem('popupType');
    }
}

// Gọi hàm kiểm tra popup khi trang đã tải xong
window.onload = checkAndShowPopup;

// Hàm xóa phần tử khỏi DOM
function removeCartItem(petId) {
    var cartItem = document.querySelector(`.invoice-item[data-id="${petId}"]`);
    if (cartItem) {
        cartItem.remove();
    }
    
    // Xóa khỏi invoice check nếu có
    var invoiceCheckItem = document.querySelector(`.invoice-check-item[data-id="${petId}"]`);
    if (invoiceCheckItem) {
        invoiceCheckItem.remove();
    }

    // Kiểm tra xem còn sản phẩm nào trong giỏ hàng không
    var remainingItems = document.querySelectorAll('.invoice-item');
    if (remainingItems.length === 0) {
        // Nếu không còn sản phẩm nào, tải lại trang sau 1 giây
        location.reload();

    } else {
        // Nếu còn sản phẩm, cập nhật tổng giá và số lượng
        updateCartSummary();
    }
}

// Hàm cập nhật tổng giá và số lượng
function updateCartSummary() {
    // Cập nhật tổng số lượng
    var totalQuantity = 0;
    var quantityElements = document.querySelectorAll('.quantity');
    quantityElements.forEach(function(element) {
        totalQuantity += parseInt(element.textContent);
    });
    var totalQuantityElement = document.querySelector('.total-quantity');
    if (totalQuantityElement) {
        totalQuantityElement.textContent = totalQuantity;
    }

    // Cập nhật tổng giá
    var totalAmount = 0;
    var priceElements = document.querySelectorAll('.totalPrice');
    priceElements.forEach(function(element) {
        totalAmount += parseInt(element.textContent.replace(/[^0-9]/g, ''));
    });
    var totalAmountElement = document.querySelector('.total-amount');
    if (totalAmountElement) {
        totalAmountElement.textContent = totalAmount.toLocaleString('vi-VN') + 'đ';
    }

    // Cập nhật số lượng sản phẩm trong giỏ hàng (nếu có hiển thị)
    var cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalQuantity;
    }
}
// Hàm hiển thị cửa sổ popup
function showPopup(message, type = "success") {
    var popup = document.getElementById('popup-notification');
    var popupMessage = document.getElementById('popup-message');
    popupMessage.textContent = message;
    popup.className = 'popup-notification ' + type;
    popup.style.display = 'block';
    checkCartItems();

    // Tự động ẩn popup sau 2 giây
    setTimeout(function() {
        popup.style.display = 'none';
    }, 1000);
}

 // cập nhật lại số lượng
 function updateQuantity(itemId, quantity) {
    console.log(`Đang gửi yêu cầu cập nhật: itemId=${itemId}, quantity=${quantity}`);

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../config/update-quantity.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {

        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success ) {
                checkCartItems();
            } else {
                alert('Lỗi: ' + response.message);
            }
        } else {
            console.error('Lỗi khi gửi yêu cầu. Status:', xhr.status);
            alert('Lỗi khi gửi yêu cầu đến server');
        }
    };

    xhr.onerror = function() {
        console.error('Lỗi mạng khi gửi yêu cầu');
        alert('Có lỗi xảy ra khi kết nối đến server');
    };

    xhr.send(`id=${itemId}&quantity=${quantity}`);
}


// Cập nhật tổng tiền
function updateTotalAmount() {
    // Cập nhật tổng tiền trong .order-summary
    const allInvoiceItems = document.querySelectorAll('.totalPrice');
    let totalAmount = 0;

    allInvoiceItems.forEach(item => {
        const itemPrice = parseInt(item.innerText.replace(/[^0-9]/g, ''), 10);
        totalAmount += itemPrice;
    });

    const totalAmountElement = document.querySelector('.total-amount');

    if (totalAmountElement) {
        // Cập nhật tổng tiền mới vào phần tử .total-amount
        totalAmountElement.innerText = totalAmount.toLocaleString('vi-VN') + 'đ';
    }
}

function updateTotalAmountSelected() {
    let totalAmount = 0;
    let checkboxes = document.querySelectorAll('.checkbox-btn-cart:checked');

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            let invoiceItem = checkbox.closest('.invoice-item');
            let totalPrice = parseFloat(invoiceItem.querySelector('.totalPrice').textContent.replace(/\D/g, '')); // Lấy tổng giá từ .totalPrice
            totalAmount += totalPrice;
        }
    });

    document.querySelector('.total-amount').innerText = totalAmount.toLocaleString('vi-VN') + 'đ';
}

// Đảm bảo chỉ gắn sự kiện một lần cho các nút cộng trừ
document.addEventListener('DOMContentLoaded', function() {
    const minusButtons = document.querySelectorAll('.quantity-btn.minus');
    const plusButtons = document.querySelectorAll('.quantity-btn.plus');

    minusButtons.forEach(button => {
        button.removeEventListener('click', handleMinusClick);
        button.addEventListener('click', handleMinusClick);
    });

    plusButtons.forEach(button => {
        button.removeEventListener('click', handlePlusClick);
        button.addEventListener('click', handlePlusClick);
    });
    checkCartItems();
    
});

function handleMinusClick() {
    const itemId = this.getAttribute('data-id');
    const quantitySpan = this.nextElementSibling;
    let quantity = parseInt(quantitySpan.textContent, 10);

    if (quantity > 1) {
        quantity--;
        quantitySpan.textContent = quantity;
        updateQuantity(itemId, quantity);
        updateInvoice(itemId, quantity);
        updateInvoiceCheck(itemId);
    }
}

function handlePlusClick() {
    const itemId = this.getAttribute('data-id');
    const quantitySpan = this.previousElementSibling;
    let quantity = parseInt(quantitySpan.textContent, 10);

    quantity++;
    quantitySpan.textContent = quantity;
    updateQuantity(itemId, quantity);
    updateInvoice(itemId, quantity);
    updateInvoiceCheck(itemId);
}

function updateInvoice(itemId, quantity) {
    const invoiceItem = document.querySelector(`.invoice-item[data-id="${itemId}"]`);
    if (!invoiceItem) {
        console.error(`Không tìm thấy invoice item với id ${itemId}`);
        return;
    }

    const priceElement = invoiceItem.querySelector('.invoice-text p:nth-child(2)');
    const totalPriceElement = invoiceItem.querySelector('.totalPrice');

    if (!priceElement || !totalPriceElement) {
        console.error('Không tìm thấy phần tử giá hoặc tổng giá');
        return;
    }

    const pricePerItem = parseInt(priceElement.innerText.replace(/[^0-9]/g, ''), 10);
    const totalPrice = pricePerItem * quantity;

    totalPriceElement.innerText = `Tổng giá: ${totalPrice.toLocaleString('vi-VN')}đ`;

    // Kiểm tra nếu không có checkbox nào được chọn
    const allCheckboxes = document.querySelectorAll('.checkbox-btn-cart');
    let anyChecked = false;
    allCheckboxes.forEach(cb => {
        if (cb.checked) {
            anyChecked = true;
        }
    });

    if (!anyChecked) {
        // Cập nhật tổng tiền mới vào phần tử .total-amount
        updateTotalAmount();
    } else {
        updateTotalAmountSelected();
    }

    // Cập nhật số lượng hiển thị
    const quantitySpan = invoiceItem.querySelector('#quantity');
    if (quantitySpan) {
        quantitySpan.textContent = quantity;
    }
}
    

function updateInvoiceCheck(petId) {
    // Cập nhật trong invoice check
    const invoiceCheckItem = document.querySelector(`.invoice-check-item[data-id="${petId}"]`);
    // Select the totalPrice element based on the item ID
    const invoiceItemPrice = document.querySelector(`.totalPrice[data-id="${petId}"]`);
    const totalPrice = parseInt(invoiceItemPrice.innerText.replace(/[^0-9]/g, ''), 10);

    if (invoiceCheckItem) {
        // Tìm phần tử chứa giá trong invoice-check-item
        const priceParagraph = invoiceCheckItem.querySelector('p:last-child'); // Giả sử giá nằm ở phần tử p cuối cùng
        
        // Cập nhật nội dung của phần tử chứa giá
        if (priceParagraph) {
            priceParagraph.innerText = 'Giá: ' + totalPrice.toLocaleString('vi-VN') + 'đ';
        }
    }
}



// Chọn tất cả select box
function toggleSelectAll(selectAllCheckbox) {
    const checkboxes = document.querySelectorAll('.checkbox-btn-cart');

    if (selectAllCheckbox.checked) {
        // Nếu chọn "Tất cả"
        checkboxes.forEach(checkbox => {
            if (!checkbox.checked) { // Chỉ chọn các checkbox chưa được chọn
                checkbox.checked = true; // Đánh dấu checkbox là đã được chọn
                selectItem(checkbox.getAttribute('data-id')); // Thực hiện hành động thêm vào danh sách đã chọn
            } 
        });

        updateTotalAmountSelected();
    } else {
        // Nếu bỏ chọn "Tất cả"
        checkboxes.forEach(checkbox => {
            checkbox.checked = false; // Bỏ chọn tất cả các checkbox
            selectItem(checkbox.getAttribute('data-id')); // Thực hiện hành động loại bỏ khỏi danh sách đã chọn
        });

        updateTotalAmount();
    }
}

// hàm tạo item selected
function selectItem(petId) {
    const invoiceCheckDiv = document.querySelector('.container-invoice-check');
    const checkbox = document.querySelector(`.checkbox-btn-cart[data-id="${petId}"]`);
    const invoiceItem = document.querySelector(`.invoice-item[data-id="${petId}"]`);
    
    // trỏ tới giá trị pet được nhấn
    const imageElement = invoiceItem.querySelector('.imgInvoice');
    const nameElement = invoiceItem.querySelector('.name-pet-cart');
    const totalPriceElement = invoiceItem.querySelector('.totalPrice');

    const imageSrc = imageElement.src;
    const name = nameElement.textContent;
    const totalPrice = parseInt(totalPriceElement.textContent.replace(/[^0-9]/g, ''), 10);
    // kiểm tra đã có chưa
    const invoiceCheckItem = document.querySelector(`.invoice-check-item[data-id="${petId}"]`);

    if (checkbox.checked) {
        if (!invoiceCheckItem) {
            // Tạo phần tử div chứa hình ảnh và thông tin sản phẩm
            const invoiceCheckItem = document.createElement('div');
            invoiceCheckItem.className = 'invoice-check-item';
            invoiceCheckItem.setAttribute('data-id', petId);

            // Tạo phần tử hình ảnh và thêm vào div
            const image = document.createElement('img');
            image.src = imageSrc;
            image.alt = name;

            // Tạo phần tử chứa tên sản phẩm
            const nameParagraph = document.createElement('p');
            nameParagraph.textContent = `Sản phẩm: ${name}`;

            // Tạo phần tử chứa tổng giá sản phẩm
            const totalPriceParagraph = document.createElement('p');
            totalPriceParagraph.textContent = 'Giá: ' + totalPrice.toLocaleString('vi-VN') + 'đ';

            // Thêm các phần tử vào trong invoiceCheckItem
            invoiceCheckItem.appendChild(image);
            invoiceCheckItem.appendChild(nameParagraph);
            invoiceCheckItem.appendChild(totalPriceParagraph);

            // Thêm invoiceCheckItem vào trong container truck
            invoiceCheckDiv.appendChild(invoiceCheckItem);
            invoiceCheckDiv.style.display = 'flex';

            updateTotalAmountSelected();
        }
    } else {
        // Xóa div khỏi truck
        if (invoiceCheckItem) {
            // nút chọn tất cả
            const checkBoxAll = document.querySelector('.checkbox-all-btn-cart');

            checkBoxAll.checked = false;

            // Xóa div khỏi truck khi hiệu ứng kết thúc
            invoiceCheckDiv.removeChild(invoiceCheckItem);

            // cập nhật lại tổng tiền
            updateTotalAmountSelected();

            // Kiểm tra nếu không có checkbox nào được chọn
            const allCheckboxes = document.querySelectorAll('.checkbox-btn-cart');
            
            let anyChecked = false;
            allCheckboxes.forEach(cb => {
                if (cb.checked) {
                    anyChecked = true;
                }
            });

            if (!anyChecked) {
                // Cập nhật tổng tiền mới vào phần tử .total-amount
                updateTotalAmount();
            }
        }
    }
}

function showConfirmModal(message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'pet-store-confirm-modal';
    
    const modalContent = document.createElement('div');
    modalContent.className = 'pet-store-modal-content';
    
    const messageElement = document.createElement('p');
    messageElement.textContent = message;
    messageElement.className = 'pet-store-modal-message';
    
    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'pet-store-button-container';
    
    const confirmButton = document.createElement('button');
    confirmButton.textContent = 'Xác nhận';
    confirmButton.className = 'pet-store-confirm-btn';
    
    const cancelButton = document.createElement('button');
    cancelButton.textContent = 'Hủy';
    cancelButton.className = 'pet-store-cancel-btn';
    
    buttonContainer.appendChild(confirmButton);
    buttonContainer.appendChild(cancelButton);
    modalContent.appendChild(messageElement);
    modalContent.appendChild(buttonContainer);
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Thêm hiệu ứng fade out khi đóng modal
    function closeModal() {
        modal.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(modal);
        }, 300);
    }
    
    confirmButton.onclick = function() {
        closeModal();
        onConfirm();
    };
    
    cancelButton.onclick = closeModal;
    
    // Cho phép đóng modal khi click bên ngoài
    modal.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    };
}

// cập nhật giới tính
document.addEventListener('DOMContentLoaded', function() {
    const genderInputs = document.querySelectorAll('.gender-pet input[type="radio"]');
    genderInputs.forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.closest('.invoice-item').getAttribute('data-id');
            const gender = this.value;
            updateGenderCart(itemId, gender);
        });
    });
});

function updateGenderCart(itemId, gender) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../config/update-gender-cart.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {

        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                console.log('Parsed response:', response);
                if (response.success) {
                    // Cập nhật data-gender attribute
                    const invoiceItem = document.querySelector(`.invoice-item[data-id="${itemId}"]`);
                    if (invoiceItem) {
                        invoiceItem.dataset.gender = gender;
                    } else {
                        console.error(`Không tìm thấy invoice item với id ${itemId}`);
                    }
                } else {
                    console.error('Lỗi từ server:', response.message);
                }
            } catch (e) {
                console.error('Lỗi khi parse JSON:', e);
                console.error('Response text gây lỗi:', xhr.responseText);
            }
        } else {
            console.error('Lỗi khi gửi yêu cầu. Status:', xhr.status);
        }
    };

    xhr.onerror = function() {
        console.error('Lỗi mạng khi gửi yêu cầu');
        alert('Có lỗi xảy ra khi kết nối đến server');
    };

    xhr.send(`pet_id=${itemId}&genderCart=${gender}`);
}


