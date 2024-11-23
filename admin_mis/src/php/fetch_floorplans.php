<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

$servername = "localhost";
$username = "u376871621_bomb_squad";
$password = "Fujiwara000!";
$dbname = "u376871621_mb_mis";

$connextion = new mysqli($servername, $username, $password, $dbname);

if ($connextion->connect_error) {
    die("Connection failed: " . $connextion->connect_error);
}

// Get sort parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Adjust SQL query based on sort
if ($sort === 'newest') {
    $sql = "SELECT id, name, image_url, created_at FROM floorplans ORDER BY created_at DESC";
} else if ($sort === 'oldest') {
    $sql = "SELECT id, name, image_url, created_at FROM floorplans ORDER BY created_at ASC";
} else {
    $sql = "SELECT id, name, image_url, created_at FROM floorplans"; // Default query
}

$result = $connextion->query($sql);

$floorPlans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $floorPlans[] = $row;
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'data' => $floorPlans
]);

$connextion->close();
?>
