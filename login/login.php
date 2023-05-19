<?php
/**
 * Takes the inputs form the user login form and validates them
 * @author Aythami Miguel Cabrera Mayor
 * @category File
 * @throws PDOException
 * @uses /funciones/functions.php
 */
session_name("loveGamingSession2023");
session_start();

include $_SERVER['DOCUMENT_ROOT'] . "/functions/functions.php";

if (isset($_SESSION["email"])) {
    header("location:/index.php");
    die();
}
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = strip_tags(trim($_POST["email"]));
        $password = strip_tags(trim($_POST["password"]));

        $connection = connection();
        $accountQuery = select("userId", $email, $connection);
        $roleId = select("roleId", $email, $connection);
        $accountQueryConstrasena = select("password", $email, $connection);
        $account = $accountQuery->rowCount();

        if ($account == 1 && password_verify($password, $accountQueryConstrasena->fetchColumn())) {
            $_SESSION['email'] = $email;
            $_SESSION['roleId'] = $roleId->fetchColumn();
            header("location:/index.php");
            die();
        } else {
            $error = "Password or email is not valid";
        }
    }
} catch (PDOException $error) {
    $error = $error->getMessage();
}
require '../parts/header.php';
?>
<div class="container">
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

    <form action="" method="post">
        <h2>Login</h2>
        <div class="form-group">
            <label>Email:</label><input type="text" name="email" class="form-control"/>
        </div>
        <div class="form-group">
            <label>Password :</label><input type="password" name="password" class="form-control" />
        </div>
        <input type="submit" value="Enviar" class="btn btn-primary mt-3"/><br />
    </form>
</div>
<?php

require $_SERVER['DOCUMENT_ROOT'] . '/parts/footer.php';
?>