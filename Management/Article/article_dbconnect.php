<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'museo_bulawan';

// Create the mysqli connection
$mysqli = new mysqli($host, $user, $password, $database);

// Check the connection
if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}
?>
