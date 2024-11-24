<?php
// Ensure JSON response
header('Content-Type: application/json');

// Start session for admin authentication
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

// Include the database connection
include 'db_connect.php';

// Retrieve and decode raw JSON input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Validate JSON decoding
if (!is_array($data)) {
    echo json_encode(['error' => 'Invalid JSON input.']);
    exit;
}

// Check required fields
if (!isset($data['appointmentID']) || !isset($data['action'])) {
    echo json_encode(['error' => 'Missing appointmentID or action.']);
    exit;
}

// Sanitize and validate input
$appointmentID = intval($data['appointmentID']);
$action = strtolower(trim($data['action'])); // Example: "approve" or "reject"

// Determine the database changes based on the action
switch ($action) {
    case 'approve':
        $status = 'Approved';
        $message = 'The appointment has been approved.';
        break;
    case 'reject':
        $status = 'Rejected';
        $message = 'The appointment has been rejected.';
        break;
    default:
        echo json_encode(['error' => 'Invalid action.']);
        exit;
}

// Prepare and execute the update statement
$sql = "UPDATE appointment SET status = ?, confirmation_date = NOW() WHERE appointmentID = ?";
$stmt = $connection->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $status, $appointmentID);

    if ($stmt->execute()) {
        echo json_encode(['success' => $message]);
    } else {
        // Log the error internally
        error_log("Error updating appointment: " . $stmt->error);
        echo json_encode(['error' => 'Failed to update the appointment. Please try again later.']);
    }

    $stmt->close();
} else {
    // Log preparation error
    error_log("Error preparing statement: " . $connection->error);
    echo json_encode(['error' => 'Server error. Please try again later.']);
}

// Close the database connection
$connection->close();
?>
