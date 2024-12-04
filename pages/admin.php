<?php
$table = isset($_GET['table']) ? $_GET['table'] : '';
?>

<link rel="stylesheet" href="../asset/css/admin.css">

<nav class="item-menu">
    <a class="link-width" href="index.php?page=admin&table=add">
        <button>Thêm sản phẩm</button>
    </a>
    <a class="link-width" href="index.php?page=admin&table=pets">
        <button>Xem sản phẩm</button>
    </a>
    <a class="link-width" href="index.php?page=admin&table=bill">
        <button>Xem đơn hàng</button>
    </a>
</nav>

<main class=" item-main">
    <?php
    switch ($table) {
        case "add":
            require_once "formAddAdmin.php";
            break;
        case "pets":
            require_once "petsAdmin.php";
            break;
        case "bill":
            require_once "bill.php";
            break;
    }
    ?>
</main>