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
        $price = strip_tags(trim($_POST["price"]));
        $productType = strip_tags(trim($_POST["productType"]));

        $connection = connection();

        $productTypeQuery = "select productTypeId from producttype where productType = :productType";
        $productTypeExecQuery = $connection->prepare($productTypeQuery);
        $productTypeExecQuery->bindParam(":productType", $productType, PDO::PARAM_STR);
        $productTypeExecQuery->execute();
        $productTypeId = $productTypeExecQuery->fetchColumn();

        //validate if you are admin and if everything is correct
        if ($releaseDate != "" && $description != "" && ($stock != "" && is_numeric($stock)) && $product != "" && ($price != "" && is_numeric($price)) && $productType != "") {
            if (!empty($_FILES['picture']['name'])) {
                if ($_FILES['picture']['size'] <= 2097152) {
                    if (strpos($tipo_archivo, 'image') !== false) {
                        // Moves image to the serve
                        if ($_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                            if (select("roleId", $_SESSION["email"], $connection)->fetchColumn() > 1) {
                                $sql = "insert into product (productTypeId, releaseDate, price, name, description, stock) values (:productTypeId, :releaseDate, :price, :name, :description, :stock)";
                                $query = $connection->prepare($sql);
                                $query->bindParam(":productTypeId", $productTypeId, PDO::PARAM_STR);
                                $query->bindParam(":releaseDate", $releaseDate, PDO::PARAM_STR);
                                $query->bindParam(":price", $price);
                                $query->bindParam(":name", $product, PDO::PARAM_STR);
                                $query->bindParam(":description", $description, PDO::PARAM_STR);
                                $query->bindParam(":stock", $stock);
                                $query->execute();

                                $success = "Product created successfully";
                                $newProductId = "SELECT productId FROM product WHERE name = :name";
                                $newProductIdQuery = $connection->prepare($newProductId);
                                $newProductIdQuery->bindParam(":name", $product, PDO::PARAM_STR);
                                $newProductIdQuery->execute();
                                $productId = $newProductIdQuery->fetchColumn();

                                $ruta_destino = '../images/products/' . $productId . "." . $extension;
                                $ruta_BBDD = '/images/products/' . $productId . "." . $extension;
                                if (move_uploaded_file($tempRoute, $ruta_destino)) {
                                    $sqlRouteUpdatePic = "update product set picture = :picture where productId = :productId";
                                    $queryRouteUpdatePic = $connection->prepare($sqlRouteUpdatePic);
                                    $queryRouteUpdatePic->bindParam(":picture", $ruta_BBDD, PDO::PARAM_STR);
                                    $queryRouteUpdatePic->bindParam(":productId", $productId);
                                    $queryRouteUpdatePic->execute();
                                    $success = "Product created successfully";
                                } else {
                                    $error = "There was an error moving the picture";
                                }
                            }
                        } else {
                            $error = "There was an error moving the picture";
                        }
                    } else {
                        $error = 'File is not valid';
                    }
                } else {
                    $error = 'File is too big, must be less than 2MB';
                }
            } else {
                if (select("roleId", $_SESSION["email"], $connection)->fetchColumn() > 1) {
                    $sql = "insert into product (productTypeId, releaseDate, price, name, description, stock) values (:productTypeId, :releaseDate, :price, :name, :description, :stock)";
                    $query = $connection->prepare($sql);
                    $query->bindParam(":productTypeId", $productTypeId, PDO::PARAM_STR);
                    $query->bindParam(":releaseDate", $releaseDate, PDO::PARAM_STR);
                    $query->bindParam(":price", $price, PDO::PARAM_STR);
                    $query->bindParam(":name", $product, PDO::PARAM_STR);
                    $query->bindParam(":description", $description, PDO::PARAM_STR);
                    $query->bindParam(":stock", $stock, PDO::PARAM_STR);
                    $query->execute();
                    $success = "Product added successfully";
                }
            }
        } else {
            $error = "Some value is not valid, please check everything and try again";
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

<div style="margin:auto; width:75%">
    <form action="" method="post" enctype="multipart/form-data">
        <h2>Add product</h2>
        <div class="form-group">
            <label>Name: *</label><input type="text" required name="product" class="form-control" />
        </div>
        <div class="form-group">
            <label>Description: *</label><input type="text" required name="description" class="form-control" />
        </div>
        <div class="form-group">
            <label>Price: *</label><input type="number" required step="0.01" name="price" class="form-control" />
        </div>
        <div class="form-group">
            <label>Release date: *</label><input type="date" required name="releaseDate" class="form-control" />
        </div>
        <div class="form-group">
            <label>Stock: *</label><input type="number" required name="stock" class="form-control" />
        </div>
        <div class="form-group">
            <label>Product type: *</label><select name="productType" required class="form-control">
                <?php
                $allTypes = 'select * from productType';
                $typesQuery = $connection->prepare($allTypes);
                $typesQuery->execute();
                $types = $typesQuery->fetchAll();
                foreach ($types as $row) {
                    echo "<option value='" . $row['productType'] . "'>" . $row['productType'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Picture:</label><input type="file" name="picture" id="picture" accept="image/*"
                class="form-control" />
        </div>
        <input type="submit" value="Enviar" class="btn btn-primary mt-2" /><br />
        <small>Fields with * are required</small>
    </form>

</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>