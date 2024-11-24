<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Include database connection
include 'db_connect.php';

// Log raw POST data
$rawInput = file_get_contents("php://input");
file_put_contents("php://stderr", "Raw Input: $rawInput\n");

// Decode the incoming JSON
$data = json_decode($rawInput, true);

// Log decoded JSON
file_put_contents("php://stderr", "Decoded JSON: " . print_r($data, true) . "\n");

// Extract variables from the decoded JSON
$appointmentId = $data['appointmentId'] ?? null;
$status = $data['status'] ?? null;

// Log extracted variables
file_put_contents("php://stderr", "Extracted appointmentId: $appointmentId\n");
file_put_contents("php://stderr", "Extracted status: $status\n");

// Validate input
if (!$appointmentId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

// Update query
$query = "
    UPDATE appointment 
    SET status = ?, confirmation_date = NOW() 
    WHERE appointmentID = ?
";

$stmt = $connextion->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare statement: ' . $connextion->error]);
    exit;
}

$stmt->bind_param("si", $status, $appointmentId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment: ' . $stmt->error]);
}
?>
