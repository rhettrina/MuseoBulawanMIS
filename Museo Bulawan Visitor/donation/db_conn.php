<?php

    $database_server = "localhost";
    $database_user = "u376871621_bomb_squad";
    $database_password = "Fujiwara000!";
    $database_name = "u376871621_mb_mis";
    $db = mysqli_connect($database_server, $database_user, $database_password, $database_name);


    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
?>