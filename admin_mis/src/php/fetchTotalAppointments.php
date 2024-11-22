<?php
// Include the database connection
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header("Content-Type: application/json"); // Ensure response is in JSON format

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

// Include database connection
include('db_connect.php');

// Check if connection was successful
if (!$connextion) {
    responseWithError('Database connection failed');
    exit();
}

// Define queries to get counts for various statistics
$queries = [
    'total_appointments' => "SELECT COUNT(*) AS total_appointments FROM form_data",
    'approved' => "SELECT COUNT(*) AS total_approved FROM form_data WHERE status = 'Approved'",
    'rejected' => "SELECT COUNT(*) AS total_rejected FROM form_data WHERE status = 'Rejected'",
    'expected_visitors' => "SELECT SUM(number_of_attendees) AS total_expected_visitors FROM form_data WHERE status = 'Approved'",
    'present' => "SELECT COUNT(*) AS total_present FROM form_data WHERE status = 'Present'"
];

// Initialize the response array
$response = [];

// Execute each query and store the result
foreach ($queries as $key => $query) {
    $result = mysqli_query($connextion, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $response[$key] = $row ? $row["total_$key"] ?? 0 : 0;
    } else {
        responseWithError("Error executing query for $key");
        exit();
    }
}

// Send the response as JSON
echo json_encode($response);

// Close the database connection
mysqli_close($connextion);

/**
 * Utility function to send an error response in JSON format
 *
 * @param string $message The error message to send
 */
function responseWithError($message) {
    echo json_encode(['error' => $message]);
    exit();
}
?>
