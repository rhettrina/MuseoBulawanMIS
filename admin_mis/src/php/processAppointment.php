<?php
header('Content-Type: application/json');

session_start();

// Include the database connection
include 'db_connect.php';

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

try {
    // Prepare and execute the update statement
    $sql = "UPDATE appointment SET status = ?, confirmation_date = NOW() WHERE appointmentID = ?";
    $stmt = $connextion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("si", $status, $appointmentID);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            error_log("Error updating appointment: " . $stmt->error);
            echo json_encode(['error' => 'Failed to update the appointment. Please try again later.']);
        }

        $stmt->close();
    } else {
        error_log("Error preparing statement: " . $connextion->error);
        echo json_encode(['error' => 'Server error. Please try again later.']);
    }
} catch (Exception $e) {
    error_log("Unexpected error: " . $e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred.']);
}

// Close the database connection
$connextion->close();

?>
