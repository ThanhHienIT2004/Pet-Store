function showOrderForm(productId) {
    // Hiển thị form đặt hàng
    var orderForm = document.getElementById('orderForm');
    var orderFormElement = document.getElementById('orderFormElement');

    orderForm.style.display = '';

    // Thêm hoặc cập nhật trường hidden input cho pet_ids
    var petIdsInput = document.getElementById('pet_ids') || document.createElement('input');
    petIdsInput.type = 'hidden';
    petIdsInput.id = 'pet_ids';
    petIdsInput.name = 'pet_ids';
    petIdsInput.value = JSON.stringify([productId]);

    // Lấy số lượng sản phẩm
    const invoiceItem = document.querySelector(`.invoice-item[data-id="${productId}"]`);
    const quantityElement = invoiceItem.querySelector('#quantity');
    const quantity = quantityElement.innerText.trim();

    // Thêm hoặc cập nhật trường hidden input cho số lượng
    var quantityInput = document.getElementById('pet_quantities') || document.createElement('input');
    quantityInput.type = 'hidden';
    quantityInput.id = 'pet_quantities';
    quantityInput.name = 'pet_quantities';
    quantityInput.value = JSON.stringify([quantity]);

    // Lấy giá trị genders
    const genderElement = invoiceItem.querySelector(`.gender-pet input[type="radio"]:checked`);
    const gender = genderElement.value;

    // Thêm hoặc cập nhật trường hidden input cho gender
    var genderInput = document.getElementById('gender') || document.createElement('input');
    genderInput.type = 'hidden';
    genderInput.id = 'gender';
    genderInput.name = 'gender';
    genderInput.value = JSON.stringify([gender]);

    // Thêm inputs vào form
    orderFormElement.appendChild(petIdsInput);
    orderFormElement.appendChild(quantityInput);
    orderFormElement.appendChild(genderInput);
    
    // Gán giá trị productId vào hidden input và cập nhật tổng giá
    updateTotalPriceForm(productId);

    // Cuộn trang đến form đặt hàng
    orderForm.scrollIntoView({ behavior: 'smooth' });
}

function showOrderAllForm() {
    // Hiển thị form đặt hàng
    var orderForm = document.getElementById('orderForm');
    var orderFormElement = document.getElementById('orderFormElement');

    orderForm.style.display = '';
    
    // Lấy tất cả petId và số lượng từ các sản phẩm trong giỏ hàng
    var petIdsAndQuantities = getAllPetIdsAndQuantities();

    // Thêm hoặc cập nhật trường hidden input cho pet_ids
    var petIdsInput = document.getElementById('pet_ids');
    if (!petIdsInput) {
        petIdsInput = document.createElement('input');
        petIdsInput.type = 'hidden';
        petIdsInput.id = 'pet_ids';
        petIdsInput.name = 'pet_ids';
        orderFormElement.appendChild(petIdsInput);
    }

    petIdsInput.value = JSON.stringify(petIdsAndQuantities.ids);

    // Thêm hoặc cập nhật trường hidden input cho pet_quantities
    var quantitiesInput = document.getElementById('pet_quantities');
    if (!quantitiesInput) {
        quantitiesInput = document.createElement('input');
        quantitiesInput.type = 'hidden';
        quantitiesInput.id = 'pet_quantities';
        quantitiesInput.name = 'pet_quantities';
        orderFormElement.appendChild(quantitiesInput);
    }

    quantitiesInput.value = JSON.stringify(petIdsAndQuantities.quantities);

    // Thêm hoặc cập nhật trường hidden input cho genders
    var gendersInput = document.getElementById('gender');
    if (!gendersInput) {
        gendersInput = document.createElement('input');
        gendersInput.type = 'hidden';
        gendersInput.id = 'gender';
        gendersInput.name = 'gender';
        orderFormElement.appendChild(gendersInput);
    }

    gendersInput.value = JSON.stringify(petIdsAndQuantities.genders);
    // Cập nhật tổng giá trị đơn hàng
    updateTotalPriceAllForm();

    // Cuộn trang đến form đặt hàng
    orderForm.scrollIntoView({
        behavior: 'smooth'
    });
}

function submitOrder() {
    const form = document.getElementById('orderFormElement');
    const formData = new FormData(form);

    const totalAmountElement = document.getElementById('total-amount-form');
    if (totalAmountElement) {
        const totalAmount = totalAmountElement.innerText.replace(/[^0-9]/g, '');
        formData.append('total_amount', totalAmount);
    }

    fetch('../config/order_config.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text(); // Chuyển từ response.json() thành response.text()
    })
    .then(text => {
        console.log('Raw response:', text); // Log raw response
        const data = JSON.parse(text); // Phân tích JSON thủ công
        if (data.success) {
            localStorage.setItem('orderMessage', data.message);
            localStorage.setItem('orderSuccess', 'true');
            closeOrderForm();
            location.reload();
        } else {
            localStorage.setItem('orderMessage', data.message);
            localStorage.setItem('orderSuccess', 'false');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Đã xảy ra lỗi khi gửi đơn hàng: ' + error.message);
    });
}

function closeOrderForm() {
    // Ẩn form
    document.getElementById('orderForm').style.display = 'none';
}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('orderFormElement');
    const submitButton = document.querySelector('.btn-submit');

    function validateForm() {
        const name = document.getElementById('name').value.trim();
        const address = document.getElementById('address').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
        const submitButton = document.querySelector('.btn-submit');

        if (name && address && phone && paymentMethod) {
            // Tất cả các trường đã được điền
            if (submitButton) {
                submitButton.style.display = 'block';
                submitButton.disabled = false;
            }
        } else {
            // Còn trường chưa được điền
            if (submitButton) {
                submitButton.style.display = 'none'; // Ẩn nút khi chưa điền đủ thông tin
                submitButton.disabled = true;
            }
        }
    }

    // Thêm event listener cho các trường input
    document.getElementById('name').addEventListener('input', validateForm);
    document.getElementById('address').addEventListener('input', validateForm);
    document.getElementById('phone').addEventListener('input', validateForm);
    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
        radio.addEventListener('change', validateForm);
    });

    // Gọi validateForm lần đầu để set trạng thái ban đầu
    validateForm();

    form.addEventListener('input', validateForm);

    // Đảm bảo rằng submitButton tồn tại trước khi thêm event listener
    if (submitButton) {
        submitButton.addEventListener('click', function(event) {
            event.preventDefault(); // Ngăn chặn form submit mặc định
            submitOrder(); // Gọi hàm submitOrder khi nút được nhấn
        });
    }
});

function updateTotalPriceForm(petId) {
    // Select the invoice item based on the item ID
    const invoiceItem = document.querySelector(`.invoice-item[data-id="${petId}"]`);

    if (invoiceItem) {
        // Extract the pet name
        const nameElement = invoiceItem.querySelector('.name-pet-cart');
        const name = nameElement ? nameElement.textContent : 'Unknown Pet';

        // Update the name in the form
        const nameFormElement = document.getElementById('name-invoice-form');
        const nameInFormElement = document.querySelector('.total-amount.nameInForm');

        nameInFormElement.style.display = '';
        nameFormElement.innerText = name;

        // Select and parse the total price
        const invoiceItemPrice = invoiceItem.querySelector('.totalPrice');
        if (invoiceItemPrice) {
            const totalPrice = parseInt(invoiceItemPrice.innerText.replace(/[^0-9]/g, ''), 10);

            // Update the total amount in the form
            document.getElementById('total-amount-form').innerText = totalPrice.toLocaleString('vi-VN') + 'đ';
        }
    } else {
        console.error(`No invoice item found for petId: ${petId}`);
    }
}

function updateTotalPriceAllForm() {
    var totalAmount = calculateTotalAmountSelected();
    document.getElementById('total-amount-form').innerText = totalAmount.toLocaleString('vi-VN') + 'đ';
}

function calculateTotalAmountSelected() {
    var checkboxes = document.querySelectorAll('.checkbox-btn-cart');
    
    // Nếu không có checkbox nào, tính tổng tất cả các mục
    if (checkboxes.length === 0) {
        return calculateTotalAmount();
    }
    
    var selectedCheckboxes = document.querySelectorAll('.checkbox-btn-cart:checked');
    
    // Nếu không có checkbox nào được chọn, cũng tính tổng tất cả các mục
    if (selectedCheckboxes.length === 0) {
        return calculateTotalAmount();
    }
    
    // Nếu có checkbox được chọn, tính tổng các mục đã chọn
    var total = 0;
    selectedCheckboxes.forEach(function(checkbox) {
        var invoiceItem = checkbox.closest('.invoice-item');
        if (invoiceItem) {
            var priceElement = invoiceItem.querySelector('.totalPrice');
            if (priceElement) {
                var price = parseInt(priceElement.innerText.replace(/[^\d]/g, ''), 10);
                total += price;
            }
        }
    });
    
    return total;
}

function calculateTotalAmount() {
    var total = 0;
    var priceElements = document.querySelectorAll('.invoice-item .totalPrice');
    
    priceElements.forEach(function(element) {
        var price = parseInt(element.innerText.replace(/[^\d]/g, ''), 10);
        total += price;
    });
    
    return total;
}

function removeFromCartUI(petId) {
    const cartItem = document.querySelector(`.invoice-item[data-id="${petId}"]`);
    if (cartItem) {
        cartItem.remove();
    }
}

function updateTotalCartAmount() {
    const totalAmountElement = document.querySelector('.order-summary .total-amount');
    const cartItems = document.querySelectorAll('.invoice-item');
    let newTotal = 0;

    cartItems.forEach(item => {
        const priceElement = item.querySelector('.totalPrice');
        if (priceElement) {
            const price = parseInt(priceElement.innerText.replace(/[^\d]/g, ''), 10);
            newTotal += price;
        }
    });

    if (totalAmountElement) {
        totalAmountElement.innerText = newTotal.toLocaleString('vi-VN') + 'đ';
    }
}

function getAllPetIdsAndQuantities() {
    const petIds = [];
    const quantities = [];
    const genders = [];
    const checkboxes = document.querySelectorAll('.checkbox-btn-cart');
    let hasChecked = false;

    // Kiểm tra checkboxes có checkbox nào được checked hay không
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            hasChecked = true;
        }
    });

    if (hasChecked) {
        // Nếu có checkboxes, chỉ lấy các pet đã được chọn
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                const invoiceItem = checkbox.closest('.invoice-item');
                if (invoiceItem) {
                    const petId = invoiceItem.getAttribute('data-id');
                    const quantity = invoiceItem.querySelector('#quantity').innerText.trim();
                    const gender = invoiceItem.querySelector('.gender-pet input[type="radio"]:checked').value;
                    console.log("Checkbox checked: ", petId, quantity, gender); // Debug info
                    if (petId && quantity !== '0' && gender) {
                        petIds.push(petId);
                        quantities.push(quantity);
                        genders.push(gender);
                    }
                }
            }
        });
    } else {
        // Nếu không có checkboxes, lấy tất cả các pet trong cart
        const cartItems = document.querySelectorAll('.invoice-item');
    
        cartItems.forEach(function(item) {
            const petId = item.getAttribute('data-id');
            const quantity = item.querySelector('#quantity').innerText.trim();
            const gender = item.querySelector('.gender-pet input[type="radio"]:checked').value;

            if (petId && quantity !== '0' && gender) {
                petIds.push(petId);
                quantities.push(quantity);
                genders.push(gender);
            }
        });
    }

    return { ids: petIds, quantities: quantities, genders: genders };
}

function removeAllFromCartUI() {
    const cartItems = document.querySelectorAll('.invoice-item');
    cartItems.forEach(item => item.remove());
}

// Hàm kiểm tra và hiển thị popup sau khi trang đã tải
function checkAndShowOrderPopup() {
    const message = localStorage.getItem('orderMessage');
    const success = localStorage.getItem('orderSuccess');
    
    if (message) {
        showPopup(message, success === 'true' ? 'success' : 'error');
        
        // Xóa thông tin từ localStorage sau khi đã hiển thị
        localStorage.removeItem('orderMessage');
        localStorage.removeItem('orderSuccess');
    }
}

// Hàm hiển thị cửa sổ popup
function showPopup(message, type = "success") {
    var popup = document.getElementById('popup-notification');
    var popupMessage = document.getElementById('popup-message');
    popupMessage.textContent = message;
    popup.className = 'popup-notification ' + type;
    popup.style.display = 'block';

    // Tự động ẩn popup sau 2 giây
    setTimeout(function() {
        popup.style.display = 'none';
    }, 1000);
}

// Thêm event listener để kiểm tra và hiển thị popup sau khi trang đã tải
window.addEventListener('load', checkAndShowOrderPopup);

document.addEventListener('DOMContentLoaded', () => {
    const bankModal = document.querySelector('.modal-bank');
    const bankCheckbox = document.getElementById('bank');
    const closeModal = bankModal.querySelector('.close');
    const confirmPaymentButton = document.getElementById('confirm-payment');

    function toggleModal(show) {
        bankModal.style.display = show ? 'block' : 'none';
    }

    bankCheckbox.addEventListener('change', () => toggleModal(bankCheckbox.checked));

    closeModal.addEventListener('click', () => toggleModal(false));

    confirmPaymentButton.addEventListener('click', (event) => {
        event.preventDefault();
        bankCheckbox.checked = true;
        toggleModal(false);
    });

    window.addEventListener('click', (event) => {
        if (event.target === bankModal) toggleModal(false);
    });
});