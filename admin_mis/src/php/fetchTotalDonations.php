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
    'total_donations' => 0,
    'accepted_donations' => 0,
    'rejected_donations' => 0,
    'total_donation_forms' => 0,
    'total_lending_forms' => 0,
    'error' => null
];

// Query to count total donations
$query = "SELECT COUNT(*) AS total FROM donations";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_donations'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch total donations: ' . mysqli_error($connextion);
}

// Query to count accepted donations
$query = "SELECT COUNT(*) AS accepted FROM donations WHERE status = 'accepted'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['accepted_donations'] = (int)$row['accepted'];
} else {
    $results['error'] = 'Failed to fetch accepted donations: ' . mysqli_error($connextion);
}

// Query to count rejected donations
$query = "SELECT COUNT(*) AS rejected FROM donations WHERE status = 'rejected'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['rejected_donations'] = (int)$row['rejected'];
} else {
    $results['error'] = 'Failed to fetch rejected donations: ' . mysqli_error($connextion);
}

// Query to count total donation forms
$query = "SELECT COUNT(*) AS total FROM donation_form";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_donation_forms'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch donation forms: ' . mysqli_error($connextion);
}

// Query to count total lending forms
$query = "SELECT COUNT(*) AS total FROM lending_form";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_lending_forms'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch lending forms: ' . mysqli_error($connextion);
}

// Return the results as JSON
echo json_encode($results);

// Close the database connection
mysqli_close($connextion);
?>
