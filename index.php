<html>

<head>
    <title>Prueba de PHP</title>
</head>

<body>

    <?php
    session_name("loveGamingSession2023");
    session_start();

    include 'functions/functions.php';
    try {
        $connection = connection();

        $AllProductsQuery = 'select * from product';
        $sentencia = $connection->prepare($AllProductsQuery);
        $sentencia->execute();
        $product = $sentencia->fetchAll();
    } catch (PDOException $error) {
        $error = $error->getMessage();
    }
    include 'parts/header.php';
    ?>

    <section class="container">
        <h1 class="text-center">Cheap games in Gran Canaria</h1>
        <p class="text-center mt-3">At Lovegaming in Gran Canaria, you can discover a wide selection of cheap games and
            immerse yourself in the thrilling world of gaming.
            Our collection features an extensive range of titles across various genres and platforms, ensuring there's
            something for every gaming enthusiast.
            Whether you're into action-packed adventures, immersive RPGs, or competitive multiplayer experiences,
            Lovegaming has you covered.
            Explore our affordable game offerings and get ready to embark on exciting gaming journeys that will keep you
            entertained for hours on end.</p>
    </section>

    <section>
        <div class="margin-50"></div>
        <div class="margin-50"></div>
    </section>

    <section>
        <div class="container">
            <div class="row" style="display:flex;justify-content:center">

                <?php
                if ($product && $sentencia->rowCount() > 0) {
                    for ($i = 0; $i < 4; $i++) {
                        ?>
                        <div class="card" style="width: 18rem; margin:10px;">
                            <img style="width:100%;height:262px;object-fit:cover" class="card-img-top"
                                src='<?php echo $product[$i]["picture"]; ?>'>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php
                                    if (isset($_SESSION["roleId"]) && $_SESSION["roleId"] > 1) {
                                        echo "ProductId: " . $product[$i]["productId"];
                                    }
                                    ?>
                                    <?php echo "<br>" . "Name: " . $product[$i]["name"]; ?>

                                </h5>
                                <p class="card-text">
                                    Release date:
                                    <?php echo $product[$i]["releaseDate"]; ?> <br>
                                    Price:
                                    <?php echo $product[$i]["price"]; ?> <br>
                                    Description:
                                    <?php echo $product[$i]["description"]; ?> <br>
                                    Stock:
                                    <?php echo $product[$i]["stock"]; ?> <br>
                                    Product type:
                                    <?php
                                    $rol = 'select productType from productType where productTypeId=' . $product[$i]["productTypeId"];
                                    $consultaRol = $connection->prepare($rol);
                                    $consultaRol->execute();
                                    $nombreRol = $consultaRol->fetchColumn();
                                    echo $nombreRol;
                                    ?> <br>

                                <div style="display:flex;justify-content:center">
                                    <?php
                                    if (isset($_SESSION["roleId"]) && $_SESSION["roleId"] > 1) {
                                        ?>
                                        <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                            style="display:flex;justify-content:center;align-items:center">
                                            <a href="/products/productList.php?delete=<?php echo $product[$i]["productId"]; ?>">
                                                <image href="/images/svg/trash-can.svg" height="25" width="25">
                                            </a>
                                        </svg>
                                        <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                            style="display:flex;justify-content:center;align-items:center">
                                            <a href="/products/productEdit.php?product=<?php echo $product[$i]["productId"]; ?>">
                                                <image href="/images/svg/pen.svg" height="25" width="25">
                                            </a>
                                        </svg>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div style="display:flex;justify-content:space-between">
                                    <?php
                                    if (isset($_SESSION["roleId"]) && $_SESSION["roleId"] < 2) {
                                        $checkWishQuery = "select * from wishlist where productId = :productId and userId = :userId";
                                        $checkWish = $connection->prepare($checkWishQuery);
                                        $checkWish->bindParam(":productId", $product[$i]['productId']);
                                        $idCheckWish = select("userId", $_SESSION["email"], $connection)->fetchColumn();
                                        $checkWish->bindParam(":userId", $idCheckWish);
                                        $checkWish->execute();
                                        if ($checkWish->rowCount() > 0) {
                                            ?>

                                            <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                                style="display:flex;justify-content:center;align-items:center">
                                                <a href="/products/productList.php?wishlist=<?php echo $product[$i]["productId"]; ?>">
                                                    <image href="/images/svg/loved-product.svg" height="25" width="25">
                                                </a>
                                            </svg>

                                            <?php
                                        } else {
                                            ?>
                                            <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                                style="display:flex;justify-content:center;align-items:center">
                                                <a href="/products/productList.php?wishlist=<?php echo $product[$i]["productId"]; ?>">
                                                    <image href="/images/svg/love.svg" height="25" width="25">
                                                </a>
                                            </svg>

                                            <?php
                                        }

                                    }
                                    ?>
                                    <?php
                                    if (isset($_SESSION["roleId"]) && $_SESSION["roleId"] < 2) {
                                        ?>

                                        <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                            style="display:flex;justify-content:center;align-items:center">
                                            <a href="/products/productList.php?cart=<?php echo $product[$i]["productId"]; ?>">
                                                <image href="/images/svg/cart.svg" height="25" width="25">
                                            </a>
                                        </svg>

                                        <?php
                                    }
                                    ?>
                                </div>
                                </p>

                            </div>
                        </div>

                        <?php
                    }

                }
                ?>
            </div>
        </div>
    </section>
    <div class="margin-50"></div>
    <div class="margin-50"></div>
    <section class="parallaxSection">
        <div class="parallaxBackgroundImg"></div>
        <div class="textParallax">
            <p>
                Immersive Gaming Experience
            </p>
        </div>
    </section>
    <div class="margin-50"></div>
    <div class="margin-50"></div>
    <section class="container">
        <div class="row">
            <div class="col-md-4" style="display:flex;margin:auto">
                <img src="/images/info/info-block-pic.jpg" alt="" srcset="" class="info-pic">
            </div>
            <div class="col-md-8 text-inf">
                <p>
                    Lovegaming is the ultimate gaming destination in Gran Canaria, catering to both casual gamers and
                    avid enthusiasts.
                    With our extensive collection of affordable games for all major platforms, you can discover new
                    adventures, epic battles, and immersive worlds at unbeatable prices.
                    Our passionate and knowledgeable team is dedicated to providing exceptional customer service,
                    helping you find the perfect game that matches your interests and preferences.
                    From action-packed thrillers to captivating story-driven experiences, we offer a diverse range of
                    titles to suit every gaming taste.
                    Visit Lovegaming today and unlock a world of excitement, entertainment, and endless gaming
                    possibilities in Gran Canaria.
                </p>
                <div style="width:100%; display:flex;justify-content:right">
                    <a class="btn btn-primary" href="/products/productList.php">
                        See our products
                    </a>
                </div>

            </div>
        </div>
    </section>
    <?php
    require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
    ?>

</body>

</html>