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

// Fetch floor plans
$sql = "SELECT id, name, image_url, created_at FROM floor_plans ORDER BY created_at DESC";
$result = $connextion->query($sql);

$floorPlans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $floorPlans[] = $row;
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($floorPlans);

$connextion->close();
?>
