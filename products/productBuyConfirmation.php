<?php
/**
 * Se encarga de realizar el registro de los user
 * @author Aythami Miguel Cabrera Mayor
 * @category File
 * @throws PDOException
 * @uses /funciones/codificar.php
 * @uses /funciones/querys.php
 */
session_name("loveGamingSession2023");
session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/functions/functions.php";

if ($_SESSION["roleId"] > 2) {
    header("location:/index.php");
    die();
}

if (!isset($_SESSION["email"]) || !isset($_SESSION["roleId"])) {
    header("location:/index.php");
    die();
}


try {
    $connection = connection();
    if (sizeof($_SESSION["cart"]) > 0) {
        $cart = $_SESSION["cart"];        
        $product = [];
        $totalPrice = 0;

        foreach ($cart as $value) {
            $AllProductsQuery = 'select price from product where productId = :productId';
            $sentencia = $connection->prepare($AllProductsQuery);
            $sentencia->bindParam(":productId", $value);
            $sentencia->execute();
            $totalPrice = $totalPrice + $sentencia->fetchColumn();
        }
    } else {
        $error = "Cart is empty";
    }
} catch (PDOException $error) {
    $error = $error->getMessage();
}


require '../parts/header.php';

if (isset($success)) {
    ?>
    <div>
        <div style="width:100%; display:flex;justify-content:center">
            <span
                style="font-family:'Roboto', 'sans-serif'; width:50%;padding: 5px ; background-color: red; border-radius:30px; color:white; text-align:center">
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


<div>

    <span>
        <strong>Total price:</strong>
        <?php
        echo $totalPrice . "€";
        ?>

    </span>
    <form action="productList.php" method="post" enctype="multipart/form-data">
        <input type="text" name="email" >
        <input type="submit" value="Buy" name="buy" /><br />
    </form>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>