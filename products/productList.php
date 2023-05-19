<?php
/**
 * Lists all the products, if you are a user it shows cart and wishlist button and if you are an admin it shows delete or edit buttons
 * @author Aythami Miguel Cabrera Mayor
 * @category File
 * @throws PDOException
 * @uses /functions/codificar.php
 * @uses /functions/functions.php
 */
session_name("loveGamingSession2023");
session_start();

include "../functions/functions.php";
try {
    $connection = connection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["buy"])) {
            if (strip_tags(trim($_POST["email"])) == $_SESSION["email"]) {
                if (isset($_SESSION["cart"])) {
                    $cart =& $_SESSION["cart"];
                    $selfUserId = select("userId", strip_tags(trim($_POST["email"])), $connection)->fetchColumn();
                    foreach ($cart as $key => $value) {
                        $productHistoryQuery = 'INSERT INTO productHistory (userId, productId, saleDate) VALUES (:userId, :productId, :saleDate)';
                        $productHistory = $connection->prepare($productHistoryQuery);
                        $productHistory->bindParam(":userId", $selfUserId);
                        $productHistory->bindParam(":productId", $value);
                        $productHistory->bindValue(":saleDate", date('Y-m-d'));
                        $productHistory->execute();
                        unset($cart[$key]);
                    }
                    $success = "Products bought successfully";
                }
            }
        }
    }
    //check session roleId and delete productID from that session roleId
    if (isset($_GET["delete"]) && $_SESSION["roleId"] > 1) {
        $idDelete = strip_tags(trim($_GET["delete"]));

        $picName = "SELECT picture from product where productId = :productId";
        $queryPic = $connection->prepare($picName);
        $queryPic->bindParam(":productId", $idDelete);
        $queryPic->execute();
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $queryPic->fetchColumn();

        if ($filePath != $_SERVER['DOCUMENT_ROOT'] . "/images/products/default.jpg") {

            $delete = "DELETE FROM product WHERE productId=:productId";
            $borrar = $connection->prepare($delete);
            $borrar->bindParam(":productId", $idDelete);
            $borrar->execute();
            unlink($filePath);
            $success = "Product deleted successfully.";

        } else {

            $delete = "DELETE FROM product WHERE productId=:productId";
            $borrar = $connection->prepare($delete);
            $borrar->bindParam(":productId", $idDelete);
            $borrar->execute();
            $success = "Product deleted successfully.";

        }
    }
    if (isset($_GET["wishlist"]) && $_SESSION["roleId"] < 2) {
        $userIdWish = select("userId", $_SESSION["email"], $connection)->fetchColumn();
        $productIdWish = strip_tags(trim($_GET["wishlist"]));

        $queryCheckWish = "select * from wishlist where productId = :productId and userId = :userId";
        $wishCheck = $connection->prepare($queryCheckWish);
        $wishCheck->bindParam(":productId", $productIdWish);
        $wishCheck->bindParam(":userId", $userIdWish);
        $wishCheck->execute();
        if ($wishCheck->rowCount() > 0) {
            $wishQuery = "delete from wishlist where userId = :userId and productId = :productId";
            $wishQueryExec = $connection->prepare($wishQuery);
            $wishQueryExec->bindParam(":userId", $userIdWish);
            $wishQueryExec->bindParam(":productId", $productIdWish);
            $wishQueryExec->execute();
            $success = "Product removed from wishlist";
        } else {
            $wishQuery = "insert into wishlist (userId, productId) values (:userId, :productId)";
            $wishQueryExec = $connection->prepare($wishQuery);
            $wishQueryExec->bindParam(":userId", $userIdWish);
            $wishQueryExec->bindParam(":productId", $productIdWish);
            $wishQueryExec->execute();
            $success = "Product added to wishlist";
        }

    }

    if (isset($_GET["cart"]) && $_SESSION["roleId"] < 2) {
        $cartValue = strip_tags(trim($_GET["cart"]));
        $_SESSION["cart"][] = $cartValue;
        $success = "product added to shopping cart successfully";
    }


    //Selects all products
    $AllProductsQuery = 'select * from product';
    $sentencia = $connection->prepare($AllProductsQuery);
    $sentencia->execute();
    $product = $sentencia->fetchAll();

} catch (PDOException $error) {
    $error = $error->getMessage();
}
?>

<?php include '../parts/header.php' ?>

<?php
if (isset($success)) {
    ?>
    <div>
        <div style="width:100%; display:flex;justify-content:center">
            <span class="successMsg">
                <?= $success ?>
            </span>
        </div>
    </div>

    <?php
}
if (isset($error)) {
    ?>
    <div class="container p-2">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                    <?= $error ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<div class="container">
    <div class="row">

        <h2 class="p-2">Product list</h2>

        <?php
        if ($product && $sentencia->rowCount() > 0) {
            foreach ($product as $row) {
                ?>
                <div class="card" style="width: 18rem; margin:10px;">
                    <img style="width:100%;height:262px;object-fit:cover" class="card-img-top"
                        src='<?php echo $row["picture"]; ?>'>
                    <div class="card-body">
                        <h5 class="card-title">
                            <?php
                            if (isset($_SESSION["roleId"]) && $_SESSION["roleId"] > 1) {
                                echo "Product id: " . $row["productId"];
                            }
                            ?>
                            <?php echo "<br>" . "<strong>Name:</strong> " . $row["name"]; ?>

                        </h5>
                        <p class="card-text">
                            <strong>Release date:</strong>
                            <?php echo $row["releaseDate"]; ?> <br>
                            <strong>Price:</strong>
                            <?php echo $row["price"] . "€"; ?> <br>
                            <strong>Description:</strong>
                            <?php echo $row["description"]; ?> <br>
                            <strong>Stock:</strong>
                            <?php echo $row["stock"]; ?> <br>
                            <strong>Product type:</strong>
                            <?php
                            $rol = 'select productType from productType where productTypeId=' . $row["productTypeId"];
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
                                    <a href="productList.php?delete=<?php echo $row["productId"]; ?>">
                                        <image href="/images/svg/trash-can.svg" height="25" width="25">
                                    </a>
                                </svg>
                                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                    style="display:flex;justify-content:center;align-items:center">
                                    <a href="productEdit.php?product=<?php echo $row["productId"]; ?>">
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
                                $checkWish->bindParam(":productId", $row['productId']);
                                $idCheckWish = select("userId", $_SESSION["email"], $connection)->fetchColumn();
                                $checkWish->bindParam(":userId", $idCheckWish);
                                $checkWish->execute();
                                if ($checkWish->rowCount() > 0) {
                                    ?>

                                    <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                        style="display:flex;justify-content:center;align-items:center">
                                        <a href="productList.php?wishlist=<?php echo $row["productId"]; ?>">
                                            <image href="/images/svg/loved-product.svg" height="25" width="25">
                                        </a>
                                    </svg>

                                    <?php
                                } else {
                                    ?>
                                    <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                        style="display:flex;justify-content:center;align-items:center">
                                        <a href="productList.php?wishlist=<?php echo $row["productId"]; ?>">
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
                                    <a href="productList.php?cart=<?php echo $row["productId"]; ?>">
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


<?php include '../parts/footer.php' ?>