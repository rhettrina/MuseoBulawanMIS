<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include 'db_connect.php';

// Get sort parameter from the query string (default is 'newest')
$sort = $_GET['sort-appointment'] ?? 'newest'; 
$order = ($sort === 'oldest') ? 'ASC' : 'DESC';

// Query to fetch sorted donations
$query = "SELECT id, 
CONCAT(first_name, ' ', last_name) AS donor_name, 
preferred_date AS appointment_date, 
preferred_time AS appointment_time, 
attendees AS number_of_attendees, 
status,
IFNULL(updated_date, 'Not Edited') AS updated_date
FROM form_data 
ORDER BY preferred_date $order, preferred_time $order"; 

$result = mysqli_query($connextion, $query);

if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$appointments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $appointments[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($appointments);
?>
