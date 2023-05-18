<?php
/**
 * Registers users and admins if the page is loaded by a superadmin
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
        if (!empty($_FILES['profilePic']['name'])) {
            $nombre_archivo = $_FILES['profilePic']['name'];
            $tipo_archivo = $_FILES['profilePic']['type'];
            $tamano_archivo = $_FILES['profilePic']['size'];
            $tempRoute = $_FILES['profilePic']['tmp_name'];
            $fileInfo = pathinfo($nombre_archivo);
            $extension = $fileInfo['extension'];
        }

        $birthDate = date('Y-m-d', strtotime($_POST['dateOfBirth']));
        $username = strip_tags(trim($_POST["username"]));
        $telephone = strip_tags(trim($_POST["telephone"]));
        $email = strip_tags(trim($_POST["email"]));
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $connection = connection();
        $emailCheck = select("userId", $email, $connection);
        $usernameCheck = check("username", $username, $connection);
        $userId = select("userId", $email, $connection);

        //validates email and if your role is superadmin
        if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
            $error = "Por favor, introduce un email válido";
        } else {
            if ($emailCheck->rowCount() == 0) {

                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    if ($usernameCheck->rowCount() == 0) {
                        if ($username != "") {
                            if (!empty($_FILES['profilePic']['name'])) {
                                if ($_FILES['profilePic']['size'] <= 2097152) {
                                    if (strpos($tipo_archivo, 'image') !== false) {
                                        // Mover la imagen cargada a una ubicación permanente en el servidor
                                        if ($_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
                                            if (isset($_POST['admin']) && $_POST["admin"] == "valid" && select("roleId", $_SESSION["email"], $connection)->fetchColumn() == 3) {
                                                $sql = "insert into user (email, password, username, telephone, dateOfBirth, roleId) values (:email, :password, :username, :telephone, :birthDate, 2)";
                                                $query = $connection->prepare($sql);
                                                $query->bindParam(":email", $email, PDO::PARAM_STR);
                                                $query->bindParam(":password", $password, PDO::PARAM_STR);
                                                $query->bindParam(":username", $username, PDO::PARAM_STR);
                                                $query->bindParam(":telephone", $telephone, PDO::PARAM_INT);
                                                $query->bindParam(":birthDate", $birthDate, PDO::PARAM_STR);
                                                $query->execute();

                                                $newUserId = select("userId", $email, $connection)->fetchColumn();
                                                $ruta_destino = '../images/profilePics/' . $newUserId . "." . $extension;
                                                $ruta_BBDD = '/images/profilePics/' . $newUserId . "." . $extension;

                                                if (move_uploaded_file($tempRoute, $ruta_destino)) {
                                                    $sqlRouteUpdatePic = "update user set profilePic = :profilePic where userId = :userId";
                                                    $queryRouteUpdatePic = $connection->prepare($sqlRouteUpdatePic);
                                                    $queryRouteUpdatePic->bindParam(":profilePic", $ruta_BBDD, PDO::PARAM_STR);
                                                    $queryRouteUpdatePic->bindParam(":userId", $newUserId);
                                                    $queryRouteUpdatePic->execute();
                                                    $success = "Admin created successfully";

                                                } else {
                                                    $error = "There was an error moving the picture";
                                                }
                                            } else {
                                                $sql = "insert into user (email, password, username, telephone, dateOfBirth) values (:email, :password, :username, :telephone, :birthDate)";
                                                $query = $connection->prepare($sql);
                                                $query->bindParam(":email", $email, PDO::PARAM_STR);
                                                $query->bindParam(":password", $password, PDO::PARAM_STR);
                                                $query->bindParam(":username", $username, PDO::PARAM_STR);
                                                $query->bindParam(":telephone", $telephone, PDO::PARAM_INT);
                                                $query->bindParam(":birthDate", $birthDate, PDO::PARAM_STR);
                                                $query->execute();
                                                if (!isset($_SESSION["email"])) {
                                                    $_SESSION["email"] = $email;
                                                    $_SESSION["roleId"] = 1;
                                                }
                                                $newUserId = select("userId", $email, $connection)->fetchColumn();
                                                $ruta_destino = '../images/profilePics/' . $newUserId . "." . $extension;
                                                $ruta_BBDD = '/images/profilePics/' . $newUserId . "." . $extension;
                                                if (move_uploaded_file($tempRoute, $ruta_destino)) {
                                                    $sqlRouteUpdatePic = "update user set profilePic = :profilePic where userId = :userId";
                                                    $queryRouteUpdatePic = $connection->prepare($sqlRouteUpdatePic);
                                                    $queryRouteUpdatePic->bindParam(":profilePic", $ruta_BBDD, PDO::PARAM_STR);
                                                    $queryRouteUpdatePic->bindParam(":userId", $newUserId);
                                                    $queryRouteUpdatePic->execute();
                                                    $success = "User created successfully";

                                                } else {
                                                    $error = "There was an error moving the picture";
                                                }
                                            }
                                        } else {
                                            $error = "Couldnt move the picture";
                                        }
                                    } else {
                                        $error = 'File type is not valid, please, upload a picture in .jpg .png or .jpeg';
                                    }
                                } else {
                                    $error = 'File is too big, must be less than 2MB';
                                }
                            } else {
                                if (isset($_POST['admin']) && $_POST["admin"] == "valid" && select("roleId", $_SESSION["email"], $connection)->fetchColumn() == 3) {
                                    $sql = "insert into user (email, password, username, telephone, dateOfBirth, roleId) values (:email, :password, :username, :telephone, :birthDate, 2)";
                                    $query = $connection->prepare($sql);
                                    $query->bindParam(":email", $email, PDO::PARAM_STR);
                                    $query->bindParam(":password", $password, PDO::PARAM_STR);
                                    $query->bindParam(":username", $username, PDO::PARAM_STR);
                                    $query->bindParam(":telephone", $telephone, PDO::PARAM_INT);
                                    $query->bindParam(":birthDate", $birthDate, PDO::PARAM_STR);
                                    $query->execute();
                                    $success = "Admin created successfully";
                                } else {
                                    $sql = "insert into user (email, password, username, telephone, dateOfBirth) values (:email, :password, :username, :telephone, :birthDate)";
                                    $query = $connection->prepare($sql);
                                    $query->bindParam(":email", $email, PDO::PARAM_STR);
                                    $query->bindParam(":password", $password, PDO::PARAM_STR);
                                    $query->bindParam(":username", $username, PDO::PARAM_STR);
                                    $query->bindParam(":telephone", $telephone, PDO::PARAM_INT);
                                    $query->bindParam(":birthDate", $birthDate, PDO::PARAM_STR);
                                    $query->execute();

                                    if (!isset($_SESSION["email"])) {
                                        $_SESSION["email"] = $email;
                                        $_SESSION["roleId"] = 1;
                                    }
                                    $success = "User created successfully";
                                }
                            }
                        } else {
                            $error = "Username cannot be empty";
                        }
                    } else {
                        $error = "Username is already taken";
                    }
                } else {
                    $error = "Email is not valid";
                }
            } else {
                $error = "Email is already taken";
            }
        }
    } else if (isset($_SESSION["email"])) {
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
?>

<div class="container">
    <form action="" method="post" enctype="multipart/form-data">
        <h2>Register</h2>
        <div class="form-group">
            <label>Email: *</label><input type="text" name="email" class="form-control" required/><br />
        </div>
        <div class="form-group">
            <label>Password: *</label><input type="password" name="password" id="passwordField" required class="form-control" />
            <br /> <button type="button" onclick="showPassword()" class="btn btn-primary">Show</button><br /><br />
        </div>
        <div class="form-group">
            <label>Username: *</label><input type="text" name="username" required class="form-control" /><br />
        </div>
        <div class="form-group">
            <label>DateOfBirth: *</label><input type="date" name="dateOfBirth" required class="form-control" /><br />
        </div>
        <div class="form-group">
            <label>Telephone: *</label><input type="tel" name="telephone" required class="form-control" /><br />
        </div>
        <div class="form-group">
            <label>ProfilePic:</label><input type="file" name="profilePic" id="profilePic" accept="image/*"
                class="form-control" /><br />
        </div>

        <?php
        if (isset($_SESSION["roleId"])) {

            if ($_SESSION["roleId"] == 3) {
                ?>
                <div class="form-group">
                    <label for="admin">Admin:</label>
                    <input type="checkbox" name="admin" value="valid">
                </div>
                <?php
            }
        }

        ?>
        <input type="submit" value="Enviar" class="btn btn-primary"/><br />
        <small>Fields with * are required</small>
    </form>
</div>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>
<script>
    function showPassword() {
        var passwordField = document.getElementById("passwordField");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }

    const fileInput = document.getElementById("profilePic");

    fileInput.addEventListener("change", () => {
        if (fileInput.files[0].size > 2097152) {
            alert("Max size is 2MB");
            fileInput.value = "";
        }
    });
</script>