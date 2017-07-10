<?php
    require_once "config.php";

    $connection = new PDO("mysql:host=$databaseServer;dbname=$databaseName;charset=utf8", $databaseUser, $databasePassword);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>