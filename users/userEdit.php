<?php
/**
 * Se encarga de mostrar todos los user, comprueba tu rol y te permite, en caso de ser superadministrador, borrar user
 * @author Aythami Miguel Cabrera Mayor
 * @category File
 * @throws PDOException
 * @uses /functions/codificar.php
 * @uses /functions/functions.php
 */
session_name("loveGamingSession2023");
session_start();

include "../functions/functions.php";
if (!isset($_SESSION["email"])) {
    header("location:../login.php");
    die();
}
try {
    $connection = connection();
    //Se comprueba el id de la sesión y si se recibe un get de delete, en ese caso
    //se comprueba el id del usuario a delete para evitar delete superadmins
    //en caso de no ser superadmin se elimina
    $email = strip_tags(trim($_SESSION["email"]));
    //Se seleccionan los user para cargar la lista con el usuario ya borrado
    $AllUsersQuery = 'select * from user where email = "' . $email . '"';
    $sentencia = $connection->prepare($AllUsersQuery);
    $sentencia->execute();
    $user = $sentencia->fetch();



    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_FILES['profilePic']['name'])) {
            $nombre_archivo = $_FILES['profilePic']['name'];
            $tipo_archivo = $_FILES['profilePic']['type'];
            $tamano_archivo = $_FILES['profilePic']['size'];
            $tempRoute = $_FILES['profilePic']['tmp_name'];
            $fileInfo = pathinfo($nombre_archivo);
            $extension = $fileInfo['extension'];
        }
        $connection = connection();

        $username = strip_tags(trim($_POST["username"]));
        $telephone = strip_tags(trim($_POST["telephone"]));
        $email = strip_tags(trim($_SESSION["email"]));
        $formEmail = strip_tags(trim($_POST["email"]));
        $password = strip_tags(trim($_POST["oldPassword"]));
        $passwordHash = password_hash(strip_tags(trim($_POST["password"])), PASSWORD_DEFAULT);

        $accountQuery = select("userId", $email, $connection);
        $accountQueryConstrasena = select("password", $email, $connection);
        $accountEmail = select("email", $email, $connection);
        $accountUsername = select("username", $email, $connection);
        $account = $accountQuery->rowCount();
        $birthDate = date('Y-m-d', strtotime($_POST['dateOfBirth']));
        $emailCheck = select("userId", $formEmail, $connection);
        $usernameCheck = check("username", $username, $connection);
        $userId = select("userId", $email, $connection)->fetchColumn();

        $picName = "SELECT profilePic from user where userId = :userId";
        $queryPic = $connection->prepare($picName);
        $queryPic->bindParam(":userId", $userId);
        $queryPic->execute();

        $filePath = $_SERVER['DOCUMENT_ROOT'] . $queryPic->fetchColumn();

        if ($account == 1 && password_verify($password, $accountQueryConstrasena->fetchColumn())) {
            //valida que el campo sea un email, y en caso de serlo comprueba si hay post de admin y si tu userId es de superadmin
            if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
                $error = "Por favor, introduce un email válido";
            } else {
                if ($username == "" || $telephone == "" || $formEmail == "" || $birthDate == "" || $password == "" || $passwordHash == "") {
                    $error = "Some required fields are empty";
                } else {
                    if ($emailCheck->rowCount() == 0 || $email == $accountEmail->fetchColumn()) {
                        if ($usernameCheck->rowCount() == 0 || $username == $accountUsername->fetchColumn()) {
                            if (!empty($_FILES['profilePic']['name'])) {
                                if ($_FILES['profilePic']['size'] <= 2097152) {
                                    if (strpos($tipo_archivo, 'image') !== false) {
                                        // Mover la imagen cargada a una ubicación permanente en el servidor
                                        if ($_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {

                                            $sql = "UPDATE user set email = :email, password = :password, username = :username, telephone = :telephone, dateOfBirth= :birthDate WHERE userId = :userId";
                                            $query = $connection->prepare($sql);
                                            $query->bindParam(":email", $formEmail, PDO::PARAM_STR);
                                            $query->bindParam(":password", $passwordHash, PDO::PARAM_STR);
                                            $query->bindParam(":username", $username, PDO::PARAM_STR);
                                            $query->bindParam(":telephone", $telephone, PDO::PARAM_INT);
                                            $query->bindParam(":birthDate", $birthDate, PDO::PARAM_STR);
                                            $query->bindParam(":userId", $userId);
                                            $query->execute();
                                            $_SESSION["email"] = $formEmail;
                                            if ($filePath != $_SERVER['DOCUMENT_ROOT'] . "/images/profilePics/default.jpg") {
                                                unlink($filePath);
                                            }

                                            $newUserId = select("userId", $formEmail, $connection)->fetchColumn();
                                            $ruta_destino = '../images/profilePics/' . $newUserId . "." . $extension;
                                            $ruta_BBDD = '/images/profilePics/' . $newUserId . "." . $extension;

                                            if (move_uploaded_file($tempRoute, $ruta_destino)) {
                                                $sqlRouteUpdatePic = "update user set profilePic = :profilePic where userId = :userId";
                                                $queryRouteUpdatePic = $connection->prepare($sqlRouteUpdatePic);
                                                $queryRouteUpdatePic->bindParam(":profilePic", $ruta_BBDD, PDO::PARAM_STR);
                                                $queryRouteUpdatePic->bindParam(":userId", $newUserId);
                                                $queryRouteUpdatePic->execute();
                                                $success = "User updated successfully";

                                            } else {
                                                $error = "There was an error moving the picture";
                                            }

                                        }
                                    } else {
                                        $error = 'El archivo cargado no es válido';
                                    }
                                } else {
                                    $error = 'El archivo cargado supera los 2MB';
                                }
                            } else {
                                //no profile pic
                                $sql = "UPDATE user set email = :email, password = :password, username = :username, telephone = :telephone, dateOfBirth= :birthDate WHERE userId = :userId";
                                $query = $connection->prepare($sql);
                                $query->bindParam(":email", $email, PDO::PARAM_STR);
                                $query->bindParam(":password", $passwordHash, PDO::PARAM_STR);
                                $query->bindParam(":username", $username, PDO::PARAM_STR);
                                $query->bindParam(":telephone", $telephone, PDO::PARAM_INT);
                                $query->bindParam(":birthDate", $birthDate, PDO::PARAM_STR);
                                $query->bindParam(":userId", $userId);
                                $query->execute();
                                $_SESSION["email"] = $formEmail;
                                $success = "User updated successfully";
                            }

                        } else {
                            $error = "Username is already taken";
                        }
                    } else {
                        $error = "Email is already taken";
                    }
                }
            }
        } else {
            $error = "Old password is not valid";
        }
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
            $userUpdatedQuery = 'select * from user where email = "' . $email . '"';
            $updatedQuery = $connection->prepare($userUpdatedQuery);
            $updatedQuery->execute();
            $userUpdated = $updatedQuery->fetch();

            if ($user && $sentencia->rowCount() > 0) {
                ?>
                <style>
                    .profilePicUserEdit {
                        width: 66px;
                        height: 66px;
                    }
                </style>
                <div style="display:flex; gap:30px">
                    <h2>Edit your account</h2>
                    <div class="profilePicUserEditDiv">
                        <img class="profilePicUserEdit" src="<?= $userUpdated["profilePic"] ?>" alt="profile picture">
                    </div>

                </div>
                <form action="" method="post" enctype="multipart/form-data">
                    <div>
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $userUpdated["email"] ?>">
                    </div>
                    <div>
                        <label for="oldPassword">Old password</label>
                        <input type="password" name="oldPassword" class="form-control" id="oldPasswordField">
                        <button type="button" class="btn btn-primary" onclick="showOldPassword()">Show</button>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="passwordField">
                        <button type="button" class="btn btn-primary" onclick="showPassword()">Show</button>
                    </div>
                    <div>
                        <label for="username">Username</label>
                        <input type="text" name="username" class="form-control" value="<?= $userUpdated["username"] ?>">
                    </div>
                    <div>
                        <label for="telephone">Telephone</label>
                        <input type="tel" name="telephone" class="form-control" value="<?= $userUpdated["telephone"] ?>">
                    </div>
                    <div>
                        <label for="dateOfBirth">Date of birth</label>
                        <input type="date" name="dateOfBirth" class="form-control"
                            value="<?= $userUpdated["dateOfBirth"] ?>">
                    </div>
                    <div>
                        <label for="profilePic">Profile picture</label>
                        <input type="file" name="profilePic" class="form-control" id="profilePic" accept="image/*" />
                    </div>
                    <div class="mt-3">
                        <input type="submit" class="btn btn-primary" value="Update">
                    </div>
                </form>
                <?php
            } else {
                ?>
                <div>Ha ocurrido un error.</div>
                <?php
            }
            ?>
        </div>
    </div>
</div>
<?php include '../parts/footer.php' ?>
<script>
    function showPassword() {
        var passwordField = document.getElementById("passwordField");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
    function showOldPassword() {
        var passwordField = document.getElementById("oldPasswordField");
        if (passwordField.type === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
    const fileInput = document.getElementById("profilePic");

    fileInput.addEventListener("change", () => {
        if (fileInput.files[0].size > 2097152) {
            alert("El tamaño máximo permitido para la imagen es de 2MB.");
            fileInput.value = "";
        }
    });
</script>