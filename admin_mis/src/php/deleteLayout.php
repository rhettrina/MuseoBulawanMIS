<?php
header('Content-Type: application/json');

// Include the database connection
require_once 'db_connect.php'; // Assumes db_connect.php is in the same directory

// Check if the connection is valid
if (!$connextion) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}

// Get the JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$id = isset($data['id']) ? $data['id'] : null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid ID"
    ]);
    exit;
}

// Prepare the DELETE statement
$stmt = $connextion->prepare("DELETE FROM `floorplans` WHERE `unique_id` = ?");
if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to prepare SQL statement"
    ]);
    exit;
}

$stmt->bind_param("s", $id);

// Execute and respond
if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Layout deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete layout: " . $stmt->error
    ]);
}

// Close resources
$stmt->close();
$connextion->close();
?>
