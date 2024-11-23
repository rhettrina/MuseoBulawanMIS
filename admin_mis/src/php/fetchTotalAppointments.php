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
    'approved_appointments' => 0,
    'rejected_appointments' => 0,
    'total_appointment_forms' => 0,
    'total_pending_appointments' => 0,
    'error' => null
];

// Query to count total appointments
$query = "SELECT COUNT(*) AS total FROM form_data";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_appointments'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch total appointments: ' . mysqli_error($connextion);
    errorHandler($results['error']);
}

// Query to count approved appointments
$query = "SELECT COUNT(*) AS approved FROM form_data WHERE status = 'Approved'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['approved_appointments'] = (int)$row['approved'];
} else {
    $results['error'] = 'Failed to fetch approved appointments: ' . mysqli_error($connextion);
    errorHandler($results['error']);
}

// Query to count rejected appointments
$query = "SELECT COUNT(*) AS rejected FROM form_data WHERE status = 'Rejected'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['rejected_appointments'] = (int)$row['rejected'];
} else {
    $results['error'] = 'Failed to fetch rejected appointments: ' . mysqli_error($connextion);
    errorHandler($results['error']);
}

// Query to count total appointment forms
$query = "SELECT COUNT(*) AS total FROM appointment_form";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_appointment_forms'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch appointment forms: ' . mysqli_error($connextion);
    errorHandler($results['error']);
}

// Query to count total pending appointments
$query = "SELECT COUNT(*) AS total FROM form_data WHERE status = 'Pending'";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_pending_appointments'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch pending appointments: ' . mysqli_error($connextion);
    errorHandler($results['error']);
}

// Return the results as JSON
echo json_encode($results);

// Close the database connection
mysqli_close($connextion);

// Function to handle errors
function errorHandler($error) {
    // Log the error to the console (for debugging purposes)
    echo "<script>console.error('Error: $error');</script>";
}
?>
