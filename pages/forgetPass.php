<link rel="stylesheet" href="../asset/css/quenpass.css">

<!-- Modal quên mật khẩu -->
<div id="forgotPasswordModal" class="forgot-password-modal" style="display: none">
    <div class="forgot-password-modal-content">
        <span id="closeForgotPasswordModalButton" class="close">&times;</span>
        <h4 class="border-bottom pb-2">QUÊN MẬT KHẨU</h4>
        <form id="forgotPasswordForm" method="POST">
            <div class="form-group">
                
                <label for="email">Nhập email</label>
                <input id="email" class="form-control" name="email" type="email" required>
            </div>
            <div class="form-group">
                <button type="submit" name="btn1">Gửi yêu cầu</button>
            </div>
        </form>
    </div>
</div>
<script src="../asset/js/forgetPass.js"></script>
