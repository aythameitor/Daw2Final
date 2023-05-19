<?php
/**
 * Shows a review list, if you are admin it shows all reviews and you can search by product
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
        if (isset($_POST["productReviewId"])) {
            $productReviewId = strip_tags(trim($_POST["productReviewId"]));
            $checkQuery = "SELECT ph.userId
        FROM productReview pr
        INNER JOIN productHistory ph ON pr.productHistoryId = ph.productHistoryId
        WHERE pr.productReviewId = :productReviewId";
            $checkStatement = $connection->prepare($checkQuery);
            $checkStatement->bindParam(":productReviewId", $productReviewId);
            $checkStatement->execute();
            $userId = $checkStatement->fetchColumn();

            $deleteQuery = "DELETE FROM productReview WHERE productReviewId = :productReviewId";
            $deleteStatement = $connection->prepare($deleteQuery);
            $deleteStatement->bindParam(":productReviewId", $productReviewId);
            $deleteStatement->execute();
            $error = "Review deleted";
        }
    }
    if (isset($_POST["productNameSearch"])) {
        echo "<script> alert 'a';</script>";
        $productNameSearch = strip_tags(trim($_POST["productNameSearch"]));

        $reviewQuery = "SELECT pr.productReviewId, pr.productReview, u.username, u.email, u.profilePic, p.name, p.picture
                FROM productReview pr
                INNER JOIN productHistory ph ON pr.productHistoryId = ph.productHistoryId
                INNER JOIN user u ON ph.userId = u.userId
                INNER JOIN product p ON ph.productId = p.productId where p.name = :productName";

        $reviews = $connection->prepare($reviewQuery);
        $reviews->bindParam(":productName", $productNameSearch);
        $reviews->execute();
        $reviews = $reviews->fetchAll();

    } else {

        $reviewQuery = "SELECT pr.productReviewId, pr.productReview, u.username, u.email, u.profilePic, p.name, p.picture
                FROM productReview pr
                INNER JOIN productHistory ph ON pr.productHistoryId = ph.productHistoryId
                INNER JOIN user u ON ph.userId = u.userId
                INNER JOIN product p ON ph.productId = p.productId";

        $reviews = $connection->query($reviewQuery)->fetchAll();

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
                class="successMsg">
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
            <h2 class="p-2">Review list</h2>
        </div>

        <?php
        if (sizeof($reviews) == 0) {
            ?>
            <div>
                <form action="" method="post">
                    <span>Search product: </span>
                    <select name="productNameSearch">
                        <?php
                        $productNameQuery = "SELECT name from product";
                        $productName = $connection->prepare($productNameQuery);
                        $productName->execute();
                        foreach ($productName->fetchAll() as $value) {
                            echo "<option value='" . $value["name"] . "'>" . $value["name"] . "</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" value="search">
                </form>
            </div>
            <?php
            echo "<p style='text-align:center'>there are no current reviews to show.<p>";
            echo "<div style='display:flex;justify-content:center'><img style='width:250px;height:250px' src='/images/not-found/nothing-found.png' alt='nothing-found' ></div>";
        } else {
            ?>
            <div>
                <form action="" method="post">
                    <span>Search product: </span>
                    <select name="productNameSearch">
                        <?php
                        $productNameQuery = "SELECT name from product";
                        $productName = $connection->prepare($productNameQuery);
                        $productName->execute();
                        foreach ($productName->fetchAll() as $value) {
                            echo "<option value='" . $value["name"] . "'>" . $value["name"] . "</option>";
                        }
                        ?>
                    </select>
                    <input type="submit" value="search">
                </form>
            </div>

            <?php
            foreach ($reviews as $review) {
                $productReview = $review['productReview'];
                $username = $review['username'];
                $profilePic = $review['profilePic'];
                $productName = $review['name'];
                $productPicture = $review['picture'];
                $productReviewId = $review['productReviewId'];
                ?>

                <div class="card" style="width: 18rem; margin:10px;">
                    <img class="card-img-top" src="<?= $productPicture ?>"
                        style="width:100%;height:262px;object-fit:cover;margin-top:10px" alt="Review image">
                    <div class="card-body">
                        <h5 class="card-title">Product:
                            <?= $productName ?>
                            <br /> Written by:
                            <?= $username ?> <img src="<?= $profilePic ?>" alt="profilePic" style="width:25px;height:25px;">
                        </h5>
                        <p class="card-text">
                            <?= $productReview ?>
                        </p>
                        <?php
                        $check = select("email", $review["email"], $connection)->fetchColumn();
                        if ($_SESSION["email"] == $check || $_SESSION["roleId"]>1) {
                            ?>
                            <form action="" method="POST">
                                <input type="hidden" name="productReviewId" value="<?= $productReviewId ?>">
                                <input type="submit" value="delete" class="btn btn-danger">
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
        }
        ?>

    </div>
</div>
<?php include '../parts/footer.php' ?>