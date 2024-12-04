document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.save-btn-pets-admin').forEach(function (button) {
        button.addEventListener('click', function () {
            var container = this.closest('.container-pets-admin');
            var id = container.getAttribute('data-id');
            var name = container.querySelector('.edit-name').value;
            var price = container.querySelector('.edit-price').value;
            var priceSale = container.querySelector('.edit-priceSale').value;
            var idLoai = container.getAttribute('data-idLoai');
            var gender = container.querySelector('.edit-gender').value;
            var description = container.querySelector('.edit-description').value; // Lấy giá trị từ textarea
            var urlImg = container.querySelector('.edit-urlImg').files[0];

            var formData = new FormData();
            formData.append('pet-id', id);
            formData.append('pet-name', name);
            formData.append('pet-price', price);
            formData.append('pet-price-sale', priceSale);
            formData.append('pet-idLoai', idLoai);
            formData.append('pet-gender', gender);
            formData.append('pet-description', description); // Thêm mô tả vào form data
            if (urlImg) {
                formData.append('pet-image', urlImg);
            }

            fetch('../config/upload_form.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Dữ liệu đã được gửi thành công:', data);
            })
            .catch(error => {
                console.error('Có lỗi xảy ra:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            });
        });
    });
});
