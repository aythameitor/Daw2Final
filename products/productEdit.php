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
if (isset($_SESSION["email"]) && $_SESSION["roleId"] == 1) {
    header("location:/index.php");
    die();
}
include $_SERVER['DOCUMENT_ROOT'] . "/functions/functions.php";

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_FILES['picture']['name'])) {
            $nombre_archivo = $_FILES['picture']['name'];
            $tipo_archivo = $_FILES['picture']['type'];
            $tamano_archivo = $_FILES['picture']['size'];
            $tempRoute = $_FILES['picture']['tmp_name'];
            $fileInfo = pathinfo($nombre_archivo);
            $extension = $fileInfo['extension'];
        }

        $releaseDate = date('Y-m-d', strtotime($_POST['releaseDate']));
        $description = strip_tags(trim($_POST["description"]));
        $stock = strip_tags(trim($_POST["stock"]));
        $product = strip_tags(trim($_POST["product"]));
        $productId = strip_tags(trim($_POST["productId"]));
        $price = strip_tags(trim($_POST["price"]));
        $productType = strip_tags(trim($_POST["productType"]));

        $connection = connection();

        $productTypeQuery = "select productTypeId from producttype where productType = :productType";
        $productTypeExecQuery = $connection->prepare($productTypeQuery);
        $productTypeExecQuery->bindParam(":productType", $productType, PDO::PARAM_STR);
        $productTypeExecQuery->execute();
        $productTypeId = $productTypeExecQuery->fetchColumn();

        // Validates your email and if you are admin

        if (!empty($_FILES['picture']['name'])) {
            if ($_FILES['picture']['size'] <= 2097152) {
                if (strpos($tipo_archivo, 'image') !== false) {
                    // Moves the image
                    if ($_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                        $ruta_destino = '../images/products/' . $product . "." . $extension;
                        $ruta_BBDD = '/images/products/' . $product . "." . $extension;
                        if (move_uploaded_file($tempRoute, $ruta_destino)) {
                            if (select("roleId", $_SESSION["email"], $connection)->fetchColumn() > 1) {
                                $sql = "UPDATE product SET productTypeId = :productTypeId, releaseDate = :releaseDate, price = :price, name = :name, description = :description, picture = :picture, stock = :stock WHERE productId = :id";
                                $query = $connection->prepare($sql);
                                $query->bindParam(":productTypeId", $productTypeId, PDO::PARAM_STR);
                                $query->bindParam(":releaseDate", $releaseDate, PDO::PARAM_STR);
                                $query->bindParam(":price", $price);
                                $query->bindParam(":name", $product, PDO::PARAM_STR);
                                $query->bindParam(":description", $description, PDO::PARAM_STR);
                                $query->bindParam(":picture", $ruta_BBDD, PDO::PARAM_STR);
                                $query->bindParam(":stock", $stock);
                                $query->bindParam(":id", $productId, PDO::PARAM_INT);
                                $query->execute();
                                $success = "Product updated successfully";
                            }
                        } else {
                            $error = "There was an error with the picture";
                        }
                    } else {
                        $error = "Couldn't move the picture";
                    }
                } else {
                    $error = 'File is not valid';
                }
            } else {
                $error = 'File must be less than 2MB';
            }
        } else {
            if (select("roleId", $_SESSION["email"], $connection)->fetchColumn() > 1) {
                $sql = "UPDATE product SET productTypeId = :productTypeId, releaseDate = :releaseDate, price = :price, name = :name, description = :description, stock = :stock WHERE productId = :id";
                $query = $connection->prepare($sql);
                $query->bindParam(":productTypeId", $productTypeId, PDO::PARAM_STR);
                $query->bindParam(":releaseDate", $releaseDate, PDO::PARAM_STR);
                $query->bindParam(":price", $price);
                $query->bindParam(":name", $product, PDO::PARAM_STR);
                $query->bindParam(":description", $description, PDO::PARAM_STR);
                $query->bindParam(":stock", $stock);
                $query->bindParam(":id", $productId, PDO::PARAM_INT);
                $query->execute();
                $success = "Product updated successfully";
            }
        }

    }
    if (isset($_SESSION["email"])) {
        $email = $_SESSION["email"];
        $connection = connection();
        $roleId = $_SESSION["roleId"];
        if ($roleId == 1) {
            header("location:" . $_SERVER['DOCUMENT_ROOT'] . "/index.php");
            die();
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $productId = strip_tags(trim($_GET["product"]));
    $sql = "SELECT * FROM product where productId = :valuee";
    $query = $connection->prepare($sql);
    $query->bindParam(":valuee", $productId);
    $query->execute();
    $queryProductValues = $query->fetch();
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div style="display:flex; align-items:center; padding:10px; gap:10px">
                <h2>Edit product</h2>
                <img src="<?= $queryProductValues["picture"]; ?>" alt="" srcset="" width="50px" height="50px">
                </div>
                
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                    <label>id:</label><input type="number" name="productId" class="form-control" value="<?= $productId; ?>"
                        readonly />
                    </div>
                    <div class="form-group">
                    <label>name:</label><input type="text" class="form-control" name="product"
                        value="<?= $queryProductValues["name"]; ?>" />
                    </div>
                    <div class="form-group">
                    <label>description:</label><input type="text" class="form-control" name="description"
                        value="<?= $queryProductValues["description"]; ?>" />
                    </div>
                    <div class="form-group">
                    <label>price:</label><input type="number" step="0.01" class="form-control" name="price"
                        value="<?= $queryProductValues["price"]; ?>" />
                    </div>
                    <div class="form-group">
                    <label>release date:</label><input type="date" class="form-control" name="releaseDate"
                        value="<?= $queryProductValues["releaseDate"]; ?>" />
                    </div>
                    <div class="form-group">
                    <label>stock:</label><input type="number" class="form-control" name="stock"
                        value="<?= $queryProductValues["stock"]; ?>" />
                    </div>
                    <div class="form-group"><label>product type:</label><select class="form-control" name="productType">
                        <?php
                        $allTypes = 'select * from productType';
                        $typesQuery = $connection->prepare($allTypes);
                        $typesQuery->execute();
                        $types = $typesQuery->fetchAll();
                        foreach ($types as $row) {
                            if ($row['productTypeId'] == $queryProductValues["productTypeId"]) {
                                echo "<option value='" . $row['productType'] . "' selected>" . $row['productType'] . "</option>";
                            } else {
                                echo "<option value='" . $row['productType'] . "'>" . $row['productType'] . "</option>";
                            }
                        }
                        ?>
                    </select></div>
                    <div class="form-group">
                    <label>picture:</label><input type="file" class="form-control" name="picture" id="picture" accept="image/*" />
                    </div>
                    <input type="submit" value="Enviar" class="btn btn-primary"/><br />
                </form>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div style="display:flex; align-items:center; padding:10px; gap:10px">
                <h2>Edit product</h2>
                </div>
                
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                    <label>id:</label><input type="number" name="productId" value="<?= $_POST["productId"]?>" class="form-control"
                        readonly />
                    </div>
                    <div class="form-group">
                    <label>name:</label><input type="text" class="form-control" value="<?= $_POST["product"] ?>" name="product"
                         />
                    </div>
                    <div class="form-group">
                    <label>description:</label><input type="text" class="form-control" value="<?= $_POST["description"]?>" name="description"
                       />
                    </div>
                    <div class="form-group">
                    <label>price:</label><input type="number" step="0.01" class="form-control" value="<?= $_POST["price"] ?>" name="price"
                         />
                    </div>
                    <div class="form-group">
                    <label>release date:</label><input type="date" class="form-control" value="<?= $_POST["releaseDate"] ?>" name="releaseDate"
                         />
                    </div>
                    <div class="form-group">
                    <label>stock:</label><input type="number" class="form-control" value="<?= $_POST["stock"] ?>" name="stock"
                         />
                    </div>
                    <div class="form-group"><label>product type:</label><select class="form-control" name="productType">
                        <?php
                        $allTypes = 'select * from productType';
                        $typesQuery = $connection->prepare($allTypes);
                        $typesQuery->execute();
                        $types = $typesQuery->fetchAll();
                        foreach ($types as $row) {
                            if ($row['productTypeId'] == $_POST["productType"]) {
                                echo "<option value='" . $row['productType'] . "' selected>" . $row['productType'] . "</option>";
                            } else {
                                echo "<option value='" . $row['productType'] . "'>" . $row['productType'] . "</option>";
                            }
                        }
                        ?>
                    </select></div>
                    <div class="form-group">
                    <label>picture:</label><input type="file" class="form-control" name="picture" id="picture" accept="image/*" />
                    </div>
                    <input type="submit" value="Enviar" class="btn btn-primary"/><br />
                </form>
            </div>
        </div>
    </div>

    <?php
}
require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>