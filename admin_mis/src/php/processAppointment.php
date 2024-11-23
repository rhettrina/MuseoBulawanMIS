<?php

session_start();

// Replace this with your actual authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized access.']);
    exit;
}

// Read the raw POST data
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

include 'db_connect.php';

// Update the appointment status using prepared statements
$stmt = $connextion->prepare("UPDATE appointment SET status = ? WHERE appointmentID = ?");
if ($stmt) {
    $stmt->bind_param("si", $newStatus, $appointmentID);
    if ($stmt->execute()) {
        // Optionally, send an email notification to the user about the approval/rejection
        // Fetch user email and name from the visitor and appointment tables
        $fetchStmt = $connextion->prepare("
            SELECT v.email, v.name, a.purpose 
            FROM appointment a 
            JOIN visitor v ON a.visitorID = v.visitorID 
            WHERE a.appointmentID = ?
        ");
        

        echo json_encode(['success' => "Appointment successfully {$newStatus}."]);
    } else {
        echo json_encode(['error' => "Error updating appointment status: " . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => "Error preparing statement: " . $connection->error]);
}

// Close the database connection
$connection->close();
?>
