<?php
// fetchTotalAppointments.php

// Set CORS headers
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

// Include the database connection
include('db_connect.php');

// Initialize an array to store results with default values
$results = [
    'total_appointments' => 0,
    'approved_appointments' => 0,
    'rejected_appointments' => 0,
    'error' => null
];

// Combined query to get total, approved, and rejected appointments
$query = "
    SELECT 
        COUNT(*) AS total_appointments,
        SUM(confirmation_date = 'Approved') AS approved_appointments,
        SUM(confirmation_date = 'Rejected') AS rejected_appointments
    FROM appointment
";

$result = mysqli_query($connextion, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_appointments'] = (int)$row['total_appointments'];
    $results['approved_appointments'] = (int)$row['approved_appointments'];
    $results['rejected_appointments'] = (int)$row['rejected_appointments'];
} else {
    $results['error'] = 'Failed to fetch appointment counts: ' . mysqli_error($connection);
    // Return JSON and exit
    echo json_encode($results);
    mysqli_close($connextion);
    exit;
}

// Return the results as JSON
echo json_encode($results);

// Close the database connection
mysqli_close($connextion);
?>
