<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, UPDATE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Respond to OPTIONS pre-flight request
}

// Include database connection
include 'db_connect.php';

// Read incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Check if the necessary data exists
if (isset($data['id'], $data['transfer_status'])) {
    $id = $data['id'];
    $transfer_status = $data['transfer_status'];

    // Ensure the ID and transfer status are valid (e.g., no empty or malformed values)
    if (empty($id) || empty($transfer_status)) {
        echo json_encode(['success' => false, 'error' => 'ID or Transfer Status is empty']);
        exit();
    }

    // Prepare the update query
    $stmt = $connextion->prepare("UPDATE Artifact SET transfer_status = ?, updated_date = NOW() WHERE id = ?");
    
    // Ensure the statement is prepared successfully
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare query']);
        exit();
    }

    // Bind parameters and execute
    $stmt->bind_param("si", $transfer_status, $id);
    
    if ($stmt->execute()) {
        // Check if any row was updated (affected rows)
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No rows were updated']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data received']);
}
?>
