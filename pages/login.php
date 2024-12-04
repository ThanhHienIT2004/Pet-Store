<link rel="stylesheet" href="../asset/css/login.css">

<!-- Modal Form Đăng Nhập -->
<div id="loginModal" class="login-modal" style="display: none;">
    <div class="login-modal-content">
        <span onclick="closeLoginModal()" id="closeLoginModalButton" class="close">&times;</span>
        <h2>Đăng Nhập</h2>
        <form id="loginForm" class="login-form">
            <div class="form-group-item">
                <label for="login-username">Tên đăng nhập:</label>
                <input type="text" id="login-username" name="username" required>
            </div>
            <div class="form-group-item">
                <label for="login-password">Mật khẩu:</label>
                <input type="password" id="login-password" name="password" required>
            </div>
            <div class="form-group-item">
                <label>
                    <input type="checkbox" name="status"> Ghi nhớ đăng nhập
                </label>
            </div>
            <hr>
            <div class="login-button-container">
                <input type="submit" value="Đăng Nhập">
                <button type="reset">Xóa</button>
            </div>
            <div id="error-message" class="login-error-message"></div>
            <p>Chưa có tài khoản? <a href="#" onclick="openRegisterModal(); return false;">Đăng ký</a></p>
            <p><a href="#" onclick="openForgotPasswordModal(); return false;">Quên mật khẩu?</a></p>
        </form>
    </div>
</div>

<script src="../asset/js/login.js"></script>
