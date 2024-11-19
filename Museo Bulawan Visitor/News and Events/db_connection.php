<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$host = 'localhost';      // Database host (usually localhost)
$username = 'root';       // Database username (default for XAMPP is root)
$password = '';           // Database password (default for XAMPP is empty)
$database = 'museo_bulawan';  // Database name (replace with your actual database name)

// Establish the connection
$connection = new mysqli($host, $username, $password, $database);

// Check if the connection was successful
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
?>
