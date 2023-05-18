<?php
/**
 * Adds review for each product and checks if you already have a review
 * @author Aythami Miguel Cabrera Mayor
 * @category File
 * @throws PDOException
 * @uses /funciones/codificar.php
 * @uses /funciones/querys.php
 */
session_name("loveGamingSession2023");
session_start();
if (!isset($_SESSION["email"]) || !isset($_SESSION["roleId"])) {
    header("location:/index.php");
    die();
}
include $_SERVER['DOCUMENT_ROOT'] . "/functions/functions.php";

try {
    $connection = connection();

    $userId = select("userId", $_SESSION["email"], $connection)->fetchColumn();
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $productReview = strip_tags(trim($_POST["review"]));
        $productScore = strip_tags(trim($_POST["rating"]));
        $productName = strip_tags(trim($_POST["product"]));


        if (strlen($productReview) <= 250) {
            $queryProductId = "select productId from product where name = :name";
            $productIdExec = $connection->prepare($queryProductId);
            $productIdExec->bindParam(":name", $productName);
            $productIdExec->execute();
            $productId = $productIdExec->fetchColumn();

            $productHistoryIdQuery = "select productHistoryId from producthistory where productId = :productId AND userId = :userId";
            $execProductHistoryId = $connection->prepare($productHistoryIdQuery);
            $execProductHistoryId->bindParam(":productId", $productId);
            $execProductHistoryId->bindParam(":userId", $userId);
            $execProductHistoryId->execute();
            $productHistoryId = $execProductHistoryId->fetch()[0];

            $reviewCheckQuery = "select * from productreview where productHistoryId = :productHistoryId";
            $reviewCheck = $connection->prepare($reviewCheckQuery);
            $reviewCheck->bindParam(":productHistoryId", $productHistoryId);
            $reviewCheck->execute();

            if ($reviewCheck->rowCount() > 0) {
                $error = "You already had a review for this article";
            } else {

                $reviewQuery = "insert into productreview (productHistoryId, productReview, productScore) values (:productHistoryId, :productReview, :productScore)";
                $review = $connection->prepare($reviewQuery);
                $review->bindParam(":productHistoryId", $productHistoryId);
                $review->bindParam(":productReview", $productReview);
                $review->bindParam(":productScore", $productScore);
                $review->execute();
                $success = "Review added successfully for product " . $productName;
            }
        } else {
            $error = "Text has more than 250 characters";
        }


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
    <form action="" method="post" enctype="multipart/form-data">
        <h2>Add review</h2>
        <label>name:</label>
        <select type="text" name="product">
            <?php
            $queryMyProducts = "select distinct productId from producthistory where userId = :userId";
            $myProducts = $connection->prepare($queryMyProducts);
            $myProducts->bindParam(":userId", $userId);
            $myProducts->execute();
            $myProductsList = $myProducts->fetchAll();
            $productDetails = [];
            foreach ($myProductsList as $value) {
                $queryDetails = "select * from product where productId = :productId";
                $myProductDetails = $connection->prepare($queryDetails);
                $myProductDetails->bindParam(":productId", $value["productId"]);
                $myProductDetails->execute();
                $productDetails[] = $myProductDetails->fetch();
            }
            foreach ($productDetails as $value) {
                echo "<option value='" . $value['name'] . "'> " . $value['name'] . "</option>";
            }
            ?>
        </select><br /><br />
        <div><label for="review">Review</label><br><br><textarea name="review" rows="4" cols="60" id="message"
                oninput="updateCounter()" onkeyup="limitWriting(event)"></textarea>
            <span id="counter">250</span> caracteres restantes
        </div>
        <div>
            <label for="rating">rating</label>
            <div style="display:flex;">
                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                    style="display:flex;justify-content:center;align-items:center">
                    <image href="/images/svg/empty-star.svg" height="25" width="25" onclick="fillStar(0);" id="0">
                </svg>
                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                    style="display:flex;justify-content:center;align-items:center">
                    <image href="/images/svg/empty-star.svg" height="25" width="25" onclick="fillStar(1);" id="1">
                </svg>
                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                    style="display:flex;justify-content:center;align-items:center">
                    <image href="/images/svg/empty-star.svg" height="25" width="25" onclick="fillStar(2);" id="2">
                </svg>
                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                    style="display:flex;justify-content:center;align-items:center">
                    <image href="/images/svg/empty-star.svg" height="25" width="25" onclick="fillStar(3);" id="3">
                </svg>
                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                    style="display:flex;justify-content:center;align-items:center">
                    <image href="/images/svg/empty-star.svg" height="25" width="25" onclick="fillStar(4);" id="4">
                </svg>
                <input type="hidden" id="rating" name="rating">
            </div>

        </div>

        <input type="submit" value="Send" /><br />
    </form>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>
<script>
    function updateCounter() {
        var text = document.getElementById("message").value;
        var counter = document.getElementById("counter");
        var caracteresRestantes = 250 - text.length;
        counter.textContent = caracteresRestantes;
    }
    function limitWriting(event) {
        var text = document.getElementById("message").value;
        var maxChars = 250;
        if (text.length > maxChars) {
            document.getElementById("message").value = text.substring(0, maxChars);
        }
    }
    function fillStar(id) {
        for (let index = 4; index >= 0; index--) {
            document.getElementById(index).setAttribute('href', "/images/svg/empty-star.svg");
        }
        for (let index = id; index >= 0; index--) {
            document.getElementById(index).setAttribute('href', "/images/svg/full-star.svg");
        }
        document.getElementById("rating").value = parseInt(id + 1);
    }
</script>