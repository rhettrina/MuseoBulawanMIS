<?php


$host = 'localhost';
$user = 'root';
$password = '';
$database = 'museo_bulawan';

// Create the mysqli connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
