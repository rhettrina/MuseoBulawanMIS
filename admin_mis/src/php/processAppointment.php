<?php
header('Content-Type: application/json');

session_start();

// Include the database connection
include 'db_connect.php';

// Check database connection
if (!$connextion) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}

// Retrieve and decode raw JSON input
$rawData = file_get_contents("php://input");
error_log("Raw input: " . $rawData);
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

// Validate allowed actions
$validActions = ['approve', 'reject'];
if (!in_array($action, $validActions)) {
    echo json_encode(['error' => 'Invalid action.']);
    exit;
}

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
}

// Prepare and execute the update statement
$sql = "UPDATE appointment SET status = ?, confirmation_date = NOW() WHERE appointmentID = ?";
$stmt = $connextion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $status, $appointmentID);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => htmlspecialchars($message)]);
        } else {
            echo json_encode(['error' => 'No rows updated. Either the appointment does not exist or the status is unchanged.']);
        }
    } else {
        // Log the error internally
        error_log("Error updating appointment ID $appointmentID: " . $stmt->error);
        echo json_encode(['error' => 'Failed to update the appointment. Please try again later.']);
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
