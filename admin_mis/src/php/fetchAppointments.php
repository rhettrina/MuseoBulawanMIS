<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0); 
    }

include 'db_connect.php';

// Sorting logic
$order = isset($_GET['sort']) && $_GET['sort'] === 'date-oldest' ? 'ASC' : 'DESC';

// Fetching data
$sql = "
SELECT 
    preferred_date AS date,
    CONCAT(first_name, ' ', last_name) AS donor_name,
    preferred_time AS time,
    attendees AS number
FROM form_data
ORDER BY preferred_date $order, preferred_time $order
";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

if (!$result) {
    echo json_encode(['error' => 'Failed to fetch appointments.']);
    exit;
}


$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
