<?php
// Include the database connection
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header("Content-Type: application/json");  // Ensure response is in JSON format

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include('db_connect.php');

// Check if connection was successful
if (!$connextion) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Perform the query
$query = "SELECT COUNT(*) AS total_appointments FROM form_data";
$result = mysqli_query($connextion, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        echo json_encode(['total_appointments' => $row['total_appointments']]);
        echo json_encode(['total_appointments' => $row['total_appointments']]);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
} else {
    echo json_encode(['error' => 'Error executing query']);
}

// Close the database connection
mysqli_close($connextion);
?>
