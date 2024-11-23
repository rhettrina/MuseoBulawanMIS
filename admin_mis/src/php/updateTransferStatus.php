<?php
// Include database connection
require_once('db_connect.php');

// Set the response type to JSON
header('Content-Type: application/json');

// Get the incoming request data
$data = json_decode(file_get_contents('php://input'), true);

// Validate the data
if (isset($data['artifactID']) && isset($data['transfer_status'])) {
    $artifactID = $data['artifactID'];
    $transferStatus = $data['transfer_status'];

    // Prepare the SQL statement to update the transfer status
    $sql = "UPDATE donations SET transfer_status = ? WHERE artifactID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $transferStatus, $artifactID);

    // Execute the query and check for success
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database update failed']);
    }

    $stmt->close();
} else {
    // Return an error response if required fields are missing
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}

$conn->close();
?>
