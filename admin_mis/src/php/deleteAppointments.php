<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

// Include database connection file
include 'db_connect.php';

// Check for valid HTTP method
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid HTTP method']);
    exit;
}

// Decode input data for POST or GET
$data = json_decode(file_get_contents("php://input"), true);
$appointmentId = isset($data['id']) ? intval($data['id']) : null;

if (!$appointmentId) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid or missing appointment ID']);
    exit;
}

$query = "DELETE FROM appointment WHERE appointmentID = ?";
$stmt = $connextion->prepare($query);

if (!$stmt) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to prepare database statement']);
    exit;
}

$stmt->bind_param("i", $appointmentId);

if ($stmt->execute()) {
    // Return success message
    http_response_code(200); // OK
    echo json_encode(['message' => 'Appointment deleted successfully']);
} else {
    // Return error message
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Failed to delete appointment']);
}

$stmt->close();
$connextion->close(); // Close database connection
?>
