<?php
// process_appointment.php

header('Content-Type: application/json'); // Ensure the response is in JSON format

session_start();

// Authentication Check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

// Include the database connection
include 'db_connect.php';

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Retrieve and sanitize POST data
// Since the user is sending JSON data, we need to decode it accordingly
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

$appointmentID = intval($data['appointmentID']);
$action = strtolower(trim($data['action']));

// Validate action
if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['error' => 'Invalid action.']);
    exit;
}

// Determine the new status
$newStatus = ($action === 'approve') ? 'Approved' : 'Rejected';

// Prepare and execute the update statement
// Update both 'status' and 'confirmation_date'
$stmt = $connection->prepare("UPDATE appointment SET status = ?, confirmation_date = NOW() WHERE appointmentID = ?");
if ($stmt) {
    $stmt->bind_param("si", $newStatus, $appointmentID);
    if ($stmt->execute()) {
        echo json_encode(['success' => "Appointment successfully {$newStatus}."], JSON_UNESCAPED_SLASHES);
    } else {
        // Log the error internally without exposing sensitive details to the user
        error_log("Error updating appointment status: " . $stmt->error);
        echo json_encode(['error' => 'Failed to update appointment status. Please try again later.']);
    }
    $stmt->close();
} else {
    // Log the error internally
    error_log("Error preparing statement: " . $connection->error);
    echo json_encode(['error' => 'Server error. Please try again later.']);
}

// Close the database connection
$connection->close();
?>
