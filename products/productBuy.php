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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST["delete"])) {
                unset($cart[$_POST["delete"]]);
                $_SESSION["cart"] = array_values($cart);
                $success = "product deleted from cart successfully";
            }
        }
        $product = [];
        $totalPrice = 0;

        foreach ($cart as $value) {
            $AllProductsQuery = 'select * from product where productId = :productId';
            $sentencia = $connection->prepare($AllProductsQuery);
            $sentencia->bindParam(":productId", $value);
            $sentencia->execute();
            $product[] = $sentencia->fetch();
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

<div class="container">
    <table class="table table-responsive">
        <?php
        if (isset($product)) {
            if ($product && $sentencia->rowCount() > 0) {
                ?>
                <tr>
                    <th>Picture</th>
                    <th>Release date</th>
                    <th>Price</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Product type</th>
                    <th>Delete</th>
                </tr>
                <?php
                foreach ($product as $key => $row) {
                    $totalPrice = $row["price"] + $totalPrice;
                    ?>
                    <tr>
                        <td><img style="width:50px;height:50px;object-fit:cover" src='<?php echo $row["picture"]; ?>'>
                        </td>
                        <td>
                            <?php echo $row["releaseDate"]; ?>
                        </td>
                        <td>
                            <?php echo $row["price"] . "€"; ?>
                        </td>
                        <td>
                            <?php echo $row["name"]; ?>
                        </td>
                        <td>
                            <?php echo $row["description"]; ?>
                        </td>
                        <td>
                            <?php
                            $rol = 'select productType from productType where productTypeId=' . $row["productTypeId"];
                            $consultaRol = $connection->prepare($rol);
                            $consultaRol->execute();
                            $nombreRol = $consultaRol->fetchColumn();
                            echo $nombreRol;
                            ?>
                        </td>
                        <td>
                            <form action="" method="post">
                                <button type="submit" value=""><svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                        style="display:flex;justify-content:center;align-items:center">
                                        <image href="/images/svg/delete.svg" height="25" width="25">
                                    </svg>
                                </button>
                                <input type="hidden" name="delete" value='<?= $key ?>'>
                            </form>

                        </td>
                    </tr>

                    <?php
                }
            }
            ?>
        </table>
        <div>
        <span  style="display:flex;margin:auto;justify-content:center">
            <strong>Total price:</strong>
            <?php
            echo $totalPrice;
            ?>

        </span>
        <form action="productBuyConfirmation.php" method="post" enctype="multipart/form-data" style="display:flex;margin:auto;justify-content:center">
            <input type="submit" value="Buy" name="buy" /><br />
        </form>
        </div>
        
        <?php
        }
        ?>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>