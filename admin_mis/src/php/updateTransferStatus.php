<?php
// Set headers for CORS and content type
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Handle preflight requests for CORS
    header("HTTP/1.1 204 No Content");
    exit(0);
}

// Include database connection
include 'db_connect.php';

// Read and decode JSON payload
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

// Validate the JSON payload
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode([
        'success' => false,
        'error' => 'Invalid JSON input',
        'raw_input' => $rawInput,
        'json_last_error' => json_last_error_msg() // Error details
    ]);
    exit();
}

// Check if required fields are present in the data
if (isset($data['donID'], $data['transfer_status'])) {
    $donID = $data['donID'];
    $transfer_status = $data['transfer_status'];

    // Validate `transfer_status` to prevent SQL injection or invalid inputs
    $allowedStatuses = ['Acquired', 'Failed', 'Pending'];
    if (!in_array($transfer_status, $allowedStatuses, true)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'error' => 'Invalid transfer_status value. Allowed values: Acquired, Failed, Pending.'
        ]);
        exit();
    }

    // Prepare the SQL statement
    $stmt = $connection->prepare(
        "UPDATE Artifact SET transfer_status = ?, updated_date = NOW() WHERE donatorID = ?"
    );

    if (!$stmt) {
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'success' => false,
            'error' => 'Failed to prepare statement: ' . $connection->error
        ]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param("si", $transfer_status, $donID);

    // Execute the statement
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Transfer status updated successfully.',
                'updated_status' => $transfer_status
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'No record found with the specified donID, or the status is already set to the given value.'
            ]);
        }
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to execute statement: ' . $stmt->error
        ]);
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing required fields: donID or transfer_status.'
    ]);
}

$connection->close();
?>
