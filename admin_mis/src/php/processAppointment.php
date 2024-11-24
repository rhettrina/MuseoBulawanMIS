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
if (!isset($data['appointmentID']) || !isset($data['updates'])) {
    echo json_encode(['error' => 'Missing appointmentID or updates.']);
    exit;
}

// Sanitize and validate input
$appointmentID = intval($data['appointmentID']);
$updates = $data['updates']; // Expecting an associative array for updates

// Validate the updates array
if (!is_array($updates) || empty($updates)) {
    echo json_encode(['error' => 'Invalid or empty updates array.']);
    exit;
}

// Build the SQL dynamically
$updateFields = [];
$updateValues = [];

foreach ($updates as $field => $value) {
    $updateFields[] = "`" . htmlspecialchars($field, ENT_QUOTES) . "` = ?";
    $updateValues[] = $value;
}

// Add the appointmentID at the end for the WHERE clause
$updateValues[] = $appointmentID;

// Create the SQL query
$sql = "UPDATE appointment SET " . implode(", ", $updateFields) . " WHERE appointmentID = ?";

// Prepare and execute the statement
$stmt = $connection->prepare($sql);

if ($stmt) {
    // Dynamically bind parameters
    $types = str_repeat("s", count($updateValues) - 1) . "i"; // Assume all fields are strings except the appointmentID
    $stmt->bind_param($types, ...$updateValues);

    if ($stmt->execute()) {
        echo json_encode(['success' => "Appointment successfully updated."]);
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
