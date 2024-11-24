<?php
// Set headers for CORS and content type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, UPDATE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Handle preflight requests
}

// Include database connection
include 'db_connect.php';

// Check if the required POST fields are set
if (isset($_POST['donID']) && isset($_POST['transfer_status'])) {
    $donID = $_POST['donID'];
    $transfer_status = $_POST['transfer_status'];

    // Prepare the SQL statement
    $stmt = $connextion->prepare("UPDATE Artifact SET transfer_status = ? WHERE donatorID = ?");
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo "Error preparing statement: " . $connextion->error;
        exit();
    }

    // Bind parameters (string for transfer_status, integer for donID)
    $stmt->bind_param("si", $transfer_status, $donID);

    // Execute the statement and send the appropriate response
    if ($stmt->execute()) {
        http_response_code(200); // Success
        echo "Transfer status updated successfully.";
    } else {
        http_response_code(500); // Internal Server Error
        echo "Error executing statement: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo "Missing required fields (donID or transfer_status).";
}

// Close the database connection
$connextion->close();
?>
