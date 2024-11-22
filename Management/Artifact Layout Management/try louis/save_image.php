<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header("Content-Type: application/json"); // Ensure response is JSON

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    echo json_encode(['status' => 'success']);
    exit(0);
}

// Retrieve the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!isset($data['imageData']) || !isset($data['name'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid input.',
    ]);
    exit;
}

$imageData = $data['imageData'];
$name = $data['name'];

// Remove the "data:image/png;base64," part
$filteredData = explode(',', $imageData);
if(count($filteredData) < 2){
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid image data format.',
    ]);
    exit;
}
$base64Data = $filteredData[1];

// Decode the base64 data
$decodedData = base64_decode($base64Data);

// Validate decoded data
if ($decodedData === false) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid image data.',
    ]);
    exit;
}

$servername = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";   

// Create connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connection->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed.',
        'error' => $connection->connect_error
    ]);
    exit;
}

// Prepare the SQL statement
$stmt = $connection->prepare("INSERT INTO floor_plans (name, image) VALUES (?, ?)");
if(!$stmt){
    echo json_encode([
        'status' => 'error',
        'message' => 'Database statement preparation failed.',
        'error' => $connection->error
    ]);
    exit;
}
$stmt->bind_param("sb", $name, $null);

// Send the blob data
$stmt->send_long_data(1, $decodedData);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Image saved successfully.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to save image.',
        'error' => $stmt->error
    ]);
}

// Close connections
$stmt->close();
$connection->close();
?>