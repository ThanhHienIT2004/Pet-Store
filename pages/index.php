<!DOCTYPE html>
<html lang="vi">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cửa Hàng Thú Cưng</title>

        <link rel="icon" type="image/x-icon" href="../asset/images/icon/logo.ico">
        <!-- css -->
        <link rel="stylesheet" href="../asset/css/pets.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap" rel="stylesheet">
        <!-- js -->
        <script src="../asset/js/cart.js"></script>
        <script src="../asset/js/check-cart.js"></script>
        <script src="../asset/js/detail.js"></script>
        <script src="../asset/js/login.js"></script>

        <!-- php -->
        <?php include 'DetailPet.php'; ?>
    </head>

    <body>
        <header>
            <?php require_once("../Includes/header.php"); ?>
            
        </header>
        
        <main >
            <?php require_once("main.php"); ?>
            <?php require_once ("../pages/login.php") ?>
            <?php require_once ("../pages/register.php") ?>
            <?php require_once ("../pages/forgetPass.php") ?>
            <!-- Popup Notification -->
            <div id="popup-notification" class="popup-notification">
                <p id="popup-message"></p>
            </div>

        </main>
    </body>
    
    <footer>
        <?php require_once("../Includes/footer.php"); ?>
    </footer>
    <script src="../asset/js/index.js"></script>
</html>