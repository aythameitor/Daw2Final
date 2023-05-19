<?php

/**
 * Functions for selecting fields from the database
 * @param String $field
 * @param String $email
 * @param $connection
 * @return PDOObject $query
 */
function select($field, $email, $connection)
{
    $sql = "SELECT $field FROM user WHERE email = :email";
    $query = $connection->prepare($sql);
    $query->bindParam(":email", $email, PDO::PARAM_STR);
    $query->execute();
    return $query;
}
function check($field,$value, $connection){
    $sql = "SELECT $field FROM user where $field = :valuee";
    $query = $connection->prepare($sql);
    $query->bindParam(":valuee", $value);
    $query->execute();
    return $query;
}

/**
 * Starts the connection
 * @return $connection
 */

function connection(){
    $config = include __DIR__ . '/config.php';
    $dsn = "mysql:host=" . $config['db']['host'].';dbname=' . $config['db']['name'];
    $connection = new PDO($dsn, $config['db']['user'], $config['db']['pass'], $config['db']['options']);
    return $connection;
}

function validatePhone($telephone) {
    $telephone = str_replace(' ', '', $telephone);
    $telephone = str_replace('-', '', $telephone);

    if (strlen($telephone) !== 10) {
        return false;
    }

    if (!ctype_digit($telephone)) {
        return false;
    }

    return true;
}
function validateDate($date){
    $curDate = date('Y-m-d');

    // Comparar la fecha ingresada con la fecha actual
    if ($date > $curDate) {
        return false;
    }

    // La fecha es válida
    return true;
}