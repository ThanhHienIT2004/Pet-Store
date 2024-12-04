<link rel="stylesheet" href="../asset/css/pets.css">
<section>

        <div class="banner">
            <img src="../asset/images/banner/dau.jpg" alt="Image 1">
        </div>

    <div class="text">
        CUNG CẤP NHỮNG LOẠI THÚ CƯNG 
        CỰC KÌ ĐA DẠNG VÀ DỄ THƯƠNG
        <a href="javascript:void(0);" onclick="scrollToMiddle()">
            <button class="buy-now">XEM NGAY</button>
        </a>
        <script>
            function scrollToMiddle() {
                window.scrollTo({
                top: document.body.scrollHeight / 5, // Cuộn đến giữa trang
                behavior: 'smooth'  // Cuộn mượt mà
            });
            }
        </script>

    </div>

<script>
    window.addEventListener('scroll', function() {
    let scrollPosition = window.scrollY;
    let banner = document.querySelector('.banner img');

    // Điều chỉnh vị trí của ảnh khi cuộn
    banner.style.transform = `translateY(${scrollPosition * -0.1}px)`; /* Tốc độ cuộn chậm hơn */
});

</script>
</section>


<section class="pet">
    <div>
        <h2>Chó</h2>
        <a href="index.php?page=dog"><img src="../asset/images/dog/head.png" alt="Chó"></a>
        <h3>Chó là loài vật nuôi trung thành và đáng yêu.</h3>
    </div>
    <div>
        <h2>Mèo</h2>
        <a href="index.php?page=cat"><img src="../asset/images/cat/head.png" alt="Mèo"></a>
        <h3>Mèo là loài vật nuôi thanh lịch và dễ thương.</h3>
    </div>
    <div>
        <h2>Vẹt</h2>
        <a href="index.php?page=parrot"><img src="../asset/images/parrot/head.png" alt="Vẹt"></a>
        <h3>Chim là loài vật nuôi đáng yêu và đáng eu</h3>
    </div>

</section>



<section id="product">
        <h1>Thú Cưng Cháy Hàng</h1>
    <?php require_once("hot.php"); ?>
    <h1>Thú Cưng</h1>
    <?php require_once("pets.php"); ?>
</section>