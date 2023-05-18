<?php
/**
 * Header for every page, shows a different header depending on your user or if you are not logged
 * @author Aythami Miguel Cabrera Mayor
 * @category File
 */
if (isset($_POST["close"])) {

    unset($_SESSION["email"]);
    unset($_SESSION["roleId"]);

    if (isset($_SESSION["cart"])) {
        unset($_SESSION["cart"]);
    }

    header("location:/login/login.php");
    die();
}

$connection = connection();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Love gaming</title>
    <link rel="stylesheet" href="/styles/main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>
    <div class="contain">
        <div class="header">

            <?php
            if (isset($_SESSION["email"])) {
                if (isset($_SESSION["roleId"])) {
                    switch ($_SESSION["roleId"]) {
                        case 1:
                            ?>
                            <nav class="navbar navbar-expand-lg">
                                <div>
                                    <a href="/" class="navbar-brand">
                                        <img src="/images/logo/love-gaming-logo.png" alt="Logo" class="navbar-logo">
                                    </a>
                                </div>
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse menu-list" style="justify-content:right;" id="navbarNav">
                                    <ul class="navbar-nav" style="column-gap:30px">
                                        <li class="nav-item active">
                                            <a href="/users/userEdit.php">Edit your account</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/productList.php">See products</a>
                                        </li>
                                        <li class="nav-item">
                                            <?php
                                            if (isset($_SESSION["cart"])) {
                                                if (sizeof($_SESSION["cart"]) > 0) {
                                                    ?>

                                                    <a href="/products/productBuy.php">Buy products</a>

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/productWishlist.php">See product wishlist</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/producthistorylist.php">See product history</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/reviews/addReview.php">Add review</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/reviews/reviewList.php">See review list</a>
                                        </li>
                                        <li class="nav-item">
                                            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" style="margin:auto">
                                                <button type="submit" name="close" value="close" style="border:hidden;background:none">
                                                    <img src="/images/exit/log_out.png" alt="logout" style="width:40px;height:25px;">
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <?php
                            break;

                        case 2:
                            ?>
                            <nav class="navbar navbar-expand-lg">
                                <div>
                                    <a href="/" class="navbar-brand">
                                        <img src="/images/logo/love-gaming-logo.png" alt="Logo" class="navbar-logo">
                                    </a>
                                </div>
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse menu-list" style="justify-content:right;" id="navbarNav">
                                    <ul class="navbar-nav" style="column-gap:30px">
                                        <li class="nav-item active">
                                            <a href="/login/register.php">Register</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/users/showUsers.php">Users</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/users/userEdit.php">Edit your account</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/productAdd.php">Add product</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/productList.php">See products</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/producthistorylist.php">See product history</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/reviews/reviewList.php">See review list</a>
                                        </li>
                                        <li class="nav-item">
                                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" style="margin:auto">
                                                <button type="submit" name="close" value="close" style="border:hidden;background:none">
                                                    <img src="/images/exit/log_out.png" alt="logout" style="width:40px;height:25px;">
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <?php
                            break;
                        case 3:
                            ?>
                            <nav class="navbar navbar-expand-lg">
                                <div>
                                    <a href="/" class="navbar-brand">
                                        <img src="/images/logo/love-gaming-logo.png" alt="Logo" class="navbar-logo">
                                    </a>
                                </div>
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse menu-list" style="justify-content:right;" id="navbarNav">
                                    <ul class="navbar-nav" style="column-gap:30px">
                                        <li class="nav-item active">
                                            <a href="/login/register.php" class="nav-link">Register</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/users/showUsers.php" class="nav-link">Users</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/users/userEdit.php" class="nav-link">Edit your account</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/productAdd.php" class="nav-link">Add product</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/products/productList.php" class="nav-link">See products</a>
                                        <li class="nav-item">
                                            <a href="/products/producthistorylist.php" class="nav-link">See product history</a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="/reviews/reviewList.php">See review list</a>
                                        </li>
                                        <li class="nav-item">
                                        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" style="margin:auto">
                                                <button type="submit" name="close" value="close" style="border:hidden;background:none">
                                                    <img src="/images/exit/log_out.png" alt="logout" style="width:40px;height:25px;">
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <?php
                            break;
                    }
                }
            } else {
                ?>
                <nav class="navbar navbar-expand-lg">
                    <div>
                        <a href="/" class="navbar-brand">
                            <img src="/images/logo/love-gaming-logo.png" alt="Logo" class="navbar-logo">
                        </a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse menu-list" style="justify-content:right;" id="navbarNav">
                        <ul class="navbar-nav" style="column-gap:30px">
                            <li class="nav-item active">
                                <a href="/login/register.php" class="nav-link">Register</a>
                            </li>
                            <li class="nav-item">
                                <a href="/login/login.php" class="nav-link">Login</a>
                            </li>
                            <li class="nav-item">
                                <a href="/products/productList.php" class="nav-link">See products</a>
                            </li>
                        </ul>
                    </div>
                </nav>
                <?php
            }


            ?>
        </div>
        <div class="bannerContainer">
            <img src="/images/svg/banner.svg" alt="bannersvg" class="banner-svg">
            <img class="banner" src="/images/banner/banner.jpg" alt="banner">
        </div>
        <div class="margin-50"></div>
        <div class="margin-50"></div>