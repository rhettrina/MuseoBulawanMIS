<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include 'db_connect.php';

// Get sort parameter from the query string (default is 'newest')
$sort = $_GET['sort'] ?? 'newest'; 
$order = ($sort === 'oldest') ? 'ASC' : 'DESC';

// Query to fetch sorted appointments with the necessary joins, sorted by created_at
$query = "
    SELECT 
        a.appointmentID AS formID, 
       v.name AS visitor_name, 
        a.preferred_time AS appointment_time, 
        a.preferred_date AS appointment_date, 
        a.population_countID AS number_of_attendees,  
        a.status, 
        a.created_at
    FROM 
        appointment AS a
    JOIN 
        visitor AS v ON a.visitorID = v.visitorID
  
    ORDER BY 
        a.created_at $order
";

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
