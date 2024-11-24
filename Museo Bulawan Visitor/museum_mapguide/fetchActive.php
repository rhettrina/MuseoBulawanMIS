<?php
// Set CORS headers to allow requests from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Database connection details
$host = "localhost"; // Replace with your host
$username = "root"; // Replace with your username
$password = ""; // Replace with your password
$dbname = "your_database_name"; // Replace with your database name

// Establish database connection
$connextion = new mysqli($host, $username, $password, $dbname);

// Check for connection errors
if ($connextion->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed: " . $connextion->connect_error,
    ]);
    exit;
}

// Prepare the SQL query
$sql = "SELECT image_path FROM floorplans WHERE active = 1";
$result = $connextion->query($sql);

// Check if the query was successful and returned a result
if ($result && $result->num_rows > 0) {
    // Fetch the image path from the query result
    $row = $result->fetch_assoc();
    echo json_encode([
        "success" => true,
        "image_path" => $row['image_path'],
    ]);
} else {
    // No active record found or query failed
    echo json_encode([
        "success" => false,
        "message" => "No active layout found",
    ]);
}

// Close the database connection
$connextion->close();
?>
