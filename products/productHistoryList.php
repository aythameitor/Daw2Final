<?php
/**
 * Shows product history, if its empty shows a message
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
    $userId = select("userId", $_SESSION["email"], $connection)->fetchColumn();
    if ($_SESSION["roleId"] > 1) {
        $productDetailsQuery = 'SELECT p.*, ph.saleDate, ph.userId
                        FROM product p
                        INNER JOIN productHistory ph ON p.productId = ph.productId';
        $productDetails = $connection->prepare($productDetailsQuery);
        $productDetails->execute();
        $productDetailsArray = $productDetails->fetchAll();
    } else {
        $productDetailsQuery = 'SELECT p.*, ph.saleDate
                        FROM product p
                        INNER JOIN productHistory ph ON p.productId = ph.productId
                        WHERE ph.userId = :userId';
        $productDetails = $connection->prepare($productDetailsQuery);
        $productDetails->bindParam(":userId", $userId);
        $productDetails->execute();
        $productDetailsArray = $productDetails->fetchAll();
    }


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
            <?php
            if ($productDetailsArray && $productDetails->rowCount() > 0) {
                ?>
                <div class="table-responsive" style="margin: auto; display: flex; justify-content: center;">
                    <table>
                        <thead>
                            <tr>
                                <?php
                                if ($_SESSION["roleId"] > 1) {
                                    echo "<th>username</th>";
                                    echo "<th>email</th>";
                                }
                                ?>
                                <th>product pic</th>
                                <th>buy date</th>
                                <th>price</th>
                                <th>name</th>
                                <th>description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            foreach ($productDetailsArray as $row) {
                                ?>
                                <tr>

                                    <?php
                                    if ($_SESSION["roleId"] > 1) {
                                        $userQuery = "select username, email from user where userId = :userId";
                                        $user = $connection->prepare($userQuery);
                                        $user->bindParam(":userId", $row["userId"]);
                                        $user->execute();
                                        $userData = $user->fetch();
                                        ?>
                                        <td>
                                            <?= $userData["username"]; ?>
                                        </td>
                                        <td>
                                            <?= $userData["email"]; ?>
                                        </td>

                                        <?php
                                    }
                                    ?>
                                    </td>
                                    <td><img style="width:50px;height:50px;object-fit:cover"
                                            src='<?php echo $row["picture"]; ?>'>
                                    </td>
                                    <td>
                                        <?php echo $row["saleDate"]; ?>
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
                                </tr>

                                <?php
                            }
            } else {
                ?>
                            <span>You don't have anything in your recent history.</span>
                            <div style='display:flex;justify-content:center'><img style='width:250px;height:250px'
                                    src='/images/not-found/nothing-found.png' alt='nothing-found'></div>
                            <?php
            }

            ?>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include '../parts/footer.php' ?>