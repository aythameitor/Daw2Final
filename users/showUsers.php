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

    if (select("roleId", $_SESSION["email"], $connection)->fetchColumn() < 2) {
        header("location:../index.php");
        die();
    }
    ;

    //Se comprueba el id de la sesión y si se recibe un get de delete, en ese caso
    //se comprueba el id del usuario a delete para evitar delete superadmins
    //en caso de no ser superadmin se elimina
    if (isset($_GET["delete"]) && $_SESSION["roleId"] == 3) {
        $idDelete = strip_tags(trim($_GET["delete"]));
        $comprobar = "SELECT roleId from user where userId = :userId";
        $consultaCompr = $connection->prepare($comprobar);
        $consultaCompr->bindParam(":userId", $idDelete);
        $consultaCompr->execute();
        $userRoleId = $consultaCompr->fetchColumn();
        //
        $picName = "SELECT profilePic from user where userId = :userId";
        $queryPic = $connection->prepare($picName);
        $queryPic->bindParam(":userId", $idDelete);
        $queryPic->execute();
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $queryPic->fetchColumn();

        if ($userRoleId != null) {
            if ($filePath != $_SERVER['DOCUMENT_ROOT'] . "/images/profilePics/default.jpg") {
                if ($userRoleId < 3) {
                    $delete = "DELETE FROM user WHERE userId=:userId";
                    $borrar = $connection->prepare($delete);
                    $borrar->bindParam(":userId", $idDelete);
                    $borrar->execute();
                    unlink($filePath);
                    $error = "User deleted successfully";
                }
            } else {
                if ($userRoleId < 3) {
                    $delete = "DELETE FROM user WHERE userId=:userId";
                    $borrar = $connection->prepare($delete);
                    $borrar->bindParam(":userId", $idDelete);
                    $borrar->execute();
                    $error = "User deleted successfully";
                }
            }
        } else {
            $error = "User not found";
        }

    }
    //Se seleccionan los user para cargar la lista con el usuario ya borrado
    $AllUsersQuery = 'select * from user';
    $sentencia = $connection->prepare($AllUsersQuery);
    $sentencia->execute();
    $user = $sentencia->fetchAll();

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
            <h2 class="p-2" style="text-align:center">User list</h2>
            <div class="table-responsive table">
                <table>
                    <thead>
                        <tr>

                            <th>Id</th>
                            <th>profile pic</th>
                            <th>username</th>
                            <th>Email</th>
                            <th>birth date</th>
                            <th>telephone</th>

                            <th>role</th>
                            <?php
                            if ($_SESSION["roleId"] >= 3) {
                                ?>
                                <th>Borrar</th>
                                <?php
                            }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($user && $sentencia->rowCount() > 0) {
                            foreach ($user as $row) {
                                ?>
                                <tr>

                                    <td>
                                        <?php echo $row["userId"]; ?>
                                    </td>
                                    <td><img style="display:flex;width:50px;height:50px;object-fit:cover;margin:auto;"
                                            src='<?php echo $row["profilePic"]; ?>'>
                                    </td>
                                    <td>
                                        <?php echo $row["username"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["email"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["dateOfBirth"]; ?>
                                    </td>
                                    <td>
                                        <?php echo $row["telephone"]; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $rol = 'select roleName from role where roleId=' . $row["roleId"];
                                        $consultaRol = $connection->prepare($rol);
                                        $consultaRol->execute();
                                        $nombreRol = $consultaRol->fetchColumn();
                                        echo $nombreRol;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($_SESSION["roleId"] >= 3) {
                                            if ($row["roleId"] < 3) {
                                                ?>
                                                <svg width="25" height="25" xmlns="http://www.w3.org/2000/svg"
                                                    style="display:flex;justify-content:center;align-items:center;margin:auto;">
                                                    <a href="showUsers.php?delete=<?php echo $row["userId"]; ?>">
                                                        <image href="/images/svg/trash-can.svg" height="25" width="25">
                                                    </a>
                                                </svg>


                                            </td>
                                        </tr>
                                        <?php
                                            }
                                        }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
       
    </div>
</div>
<?php include '../parts/footer.php' ?>