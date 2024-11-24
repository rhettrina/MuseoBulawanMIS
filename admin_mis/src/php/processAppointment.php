<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Include database connection
include 'db_connect.php';

// Decode the incoming JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Extract variables from the JSON payload
$appointmentId = $data['appointmentId'] ?? null;
$status = $data['status'] ?? null;

// Validate input
if (!$appointmentId || !$status) {
    echo json_encode(['success' => false, 'message' => 'Invalid input: appointmentId or status is missing.']);
    exit;
}

// Prepare the update query
$query = "
    UPDATE appointment 
    SET status = ?, confirmation_date = NOW() 
    WHERE appointmentID = ?
";

$stmt = $connextion->prepare($query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Failed to prepare the statement: ' . $connextion->error]);
    exit;
}

$stmt->bind_param("si", $status, $appointmentId);

// Execute the query and handle the result
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment: ' . $stmt->error]);
}
?>
