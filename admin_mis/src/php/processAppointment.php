<?php
// Ensure JSON response
header('Content-Type: application/json');

// Start session for admin authentication
session_start();

// Include the database connection
include 'db_connect.php';

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

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
$action = strtolower(trim($data['action']));

// Validate action value
if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['error' => 'Invalid action.']);
    exit;
}

// Determine the new status and confirmation date
$newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';

// Prepare and execute the update statement
$stmt = $connextion->prepare("UPDATE appointment SET status = ?, confirmation_date = NOW() WHERE appointmentID = ?");
if ($stmt) {
    $stmt->bind_param("si", $newStatus, $appointmentID);

    if ($stmt->execute()) {
        echo json_encode(['success' => "Appointment successfully {$newStatus}."]);
    } else {
        // Log the error internally
        error_log("Error updating appointment status: " . $stmt->error);
        echo json_encode(['error' => 'Failed to update appointment status. Please try again later.']);
    }

    $stmt->close();
} else {
    // Log preparation error
    error_log("Error preparing statement: " . $connextion->error);
    echo json_encode(['error' => 'Server error. Please try again later.']);
}

// Close the database connection
$connextion->close();
?>
