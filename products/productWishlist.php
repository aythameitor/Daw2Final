<?php
/**
 * Manages user wishlist, admins and superadmins dont have list
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
    $id = select("userId", $_SESSION["email"], $connection)->fetchColumn();
    //Selects item for its deletion
    if (isset($_GET["wishlist"]) && $_SESSION["roleId"] < 2) {
        $userIdWish = select("userId", $_SESSION["email"], $connection)->fetchColumn();
        $productIdWish = strip_tags(trim($_GET["wishlist"]));

            $wishQuery = "delete from wishlist where userId = :userId and productId = :productId";
            $wishQueryExec = $connection->prepare($wishQuery);
            $wishQueryExec->bindParam(":userId", $userIdWish);
            $wishQueryExec->bindParam(":productId", $productIdWish);
            $wishQueryExec->execute();
            $error = "Product removed from wishlist";
        
    }
    $wishlistQuery = 'select productId from wishlist where userId = :userId';
    $wishlist = $connection->prepare($wishlistQuery);
    $wishlist->bindParam(":userId", $id);
    $wishlist->execute();
    $WishlistArray = $wishlist->fetchAll();

    

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
    <div class="row">
        <div class="col-md-12">
            <h2 class="p-2" style="text-align:center">Wishlist</h2>

            <?php
            if (sizeof($WishlistArray) > 0) {
                ?>
                <div class="table table-responsive">
                <table>
                    <thead>
                        <tr>

                            <th>Product Pic</th>
                            <th>Release date</th>
                            <th>Price</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Stock</th>
                            <th>Type</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($WishlistArray as $id) {
                            $AllProductsQuery = 'select * from product where productId = :productId';
                            $sentencia = $connection->prepare($AllProductsQuery);
                            $sentencia->bindParam(":productId", $id["productId"]);
                            $sentencia->execute();
                            $product = $sentencia->fetchAll();
                            foreach ($product as $row) {
                                ?>
                                <tr>
                                    <td><img style="width:50px;height:50px;object-fit:cover" src='<?php echo $row["picture"]; ?>'>
                                    </td>
                                    <td>
                                        <?php echo $row["releaseDate"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["price"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["name"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["description"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["stock"]; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $rol = 'select productType from productType where productTypeId=' . $row["productTypeId"];
                                        $roleQuery = $connection->prepare($rol);
                                        $roleQuery->execute();
                                        $nombreRol = $roleQuery->fetchColumn();
                                        echo $nombreRol;
                                        ?>
                                    </td>
                                    <td>
                                        <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                            style="display:flex;justify-content:center;align-items:center">
                                            <a href="productWishlist.php?wishlist=<?php echo $row["productId"]; ?>">
                                                <image href="/images/svg/trash-can.svg" height="25" width="25">
                                            </a>
                                        </svg>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                </div>
                
                <?php
            } else {
                ?>
                
                <div  style='display:flex;justify-content:center'><span>Couldn't find any wishlisted products</span></div>
                <div style='display:flex;justify-content:center'><img style='width:250px;height:250px' src='/images/not-found/nothing-found.png' alt='nothing-found' ></div>
                <?php
            }
            ?>

        </div>
    </div>
</div>
<?php include '../parts/footer.php' ?>