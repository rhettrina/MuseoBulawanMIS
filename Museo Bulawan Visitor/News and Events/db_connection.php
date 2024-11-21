<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'localhost';     
$username = 'u376871621_bomb_squad';       
$password = 'Fujiwara000!';           
$database = 'u376871621_mb_mis'; 

// Establish the connection
$connection = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
else{
    echo'tama';
}
?>
