    // Chọn tất cả các nút "Giỏ hàng"
    const orderButtons = document.querySelectorAll("button order");
    const cart = document.getElementById("text-cart");
    
    // Lặp qua tất cả các nút và thêm sự kiện click
    orderButtons.forEach(button => {
        button.addEventListener("click", (event) => {
            const product = event.target.closest('.container-pets');
    
            // Lấy vị trí hiện tại của sản phẩm
            const productRect = product.getBoundingClientRect();
            const cartRect = cart.getBoundingClientRect();
    
            // Tạo bản sao của sản phẩm để làm hiệu ứng "bay"
            const flyingProduct = product.cloneNode(true);
            flyingProduct.classList.add("fly-to-cart");
            document.body.appendChild(flyingProduct);
    
            // Đặt vị trí ban đầu cho sản phẩm bay
            flyingProduct.style.top = `${productRect.top}px`;
            flyingProduct.style.left = `${productRect.left}px`;
    
            // Sử dụng setTimeout để đảm bảo sự thay đổi vị trí sẽ được thực hiện
            setTimeout(() => {
                flyingProduct.style.transform = `translate(${cartRect.left - productRect.left}px, ${cartRect.top - productRect.top}px) scale(0.2)`;
            }, 100);
    
            // Sau khi hoàn thành hiệu ứng, xóa phần tử bay đi
            setTimeout(() => {
                flyingProduct.remove();
            }, 1200);
        });
    });
    