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

// Read and decode JSON payload
$data = json_decode(file_get_contents('php://input'), true);

// Validate the JSON payload
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input']);
    exit();
}

// Check if required fields are present in the data
if (isset($data['donID'], $data['transfer_status'])) {
    $donID = $data['donID'];
    $transfer_status = $data['transfer_status'];

    // Prepare the SQL statement
    $stmt = $connextion->prepare("UPDATE Artifact SET transfer_status = ?, updated_date = NOW() WHERE donatorID = ?");
    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => $connextion->error]);
        exit();
    }

    // Bind parameters (string for transfer_status, integer for donID)
    $stmt->bind_param("si", $transfer_status, $donID);

    // Execute the statement and send the appropriate response
    if ($stmt->execute()) {
        http_response_code(200); // Success
        echo json_encode(['success' => true, 'message' => 'Transfer status updated successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'error' => 'Missing required fields (donID or transfer_status)']);
}

// Close the database connection
$connextion->close();
?>
