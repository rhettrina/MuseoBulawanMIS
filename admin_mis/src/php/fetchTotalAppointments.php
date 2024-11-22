<?php
// Include the database connection
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include('db_connect.php');

// Initialize an array to store results with default values
$results = [
    'total_appointments' => 0,
    'accepted_appointments' => 0,
    'rejected_appointments' => 0,
    'total_expected_visitors' => 0,
    'total_present_visitors' => 0,
    'error' => null
];

// Query to count total donations
$query = "SELECT COUNT(*) AS total FROM form_data";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_appointments'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch total donations: ' . mysqli_error($connextion);
}

// Query to count accepted donations
$query = "SELECT COUNT(*) AS accepted FROM form_data WHERE status = 'accepted'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['accepted_appointments'] = (int)$row['accepted'];
} else {
    $results['error'] = 'Failed to fetch accepted form_data: ' . mysqli_error($connextion);
}

// Query to count rejected donations
$query = "SELECT COUNT(*) AS rejected FROM form_data WHERE status = 'rejected'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['rejected_appointments'] = (int)$row['rejected'];
} else {
    $results['error'] = 'Failed to fetch rejected donations: ' . mysqli_error($connextion);
}

// Query to count total donation forms
$query = "SELECT COUNT(*) AS total FROM form_data";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_expected_visitors'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch donation forms: ' . mysqli_error($connextion);
}

$query = "SELECT COUNT(*) AS total FROM form_data";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_present_visitors'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch lending forms: ' . mysqli_error($connextion);
}

// Return the results as JSON
echo json_encode($results);

// Close the database connection
mysqli_close($connextion);
?>
