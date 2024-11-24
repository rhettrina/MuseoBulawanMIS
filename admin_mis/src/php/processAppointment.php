<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Include database connection
include 'db_connect.php';

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);
$appointmentId = $data['appointmentId'];
$status = $data['status'];

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
$stmt->bind_param("si", $status, $appointmentId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Appointment updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update appointment.']);
}
?>
