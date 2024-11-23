<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, UPDATE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Respond to OPTIONS pre-flight request
}

// Include database connection
// Database connection
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['artifactID']) || !isset($input['newStatus'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $artifactID = $input['artifactID'];
    $newStatus = $input['newStatus'];

    // Validate input
    $validStatuses = ['Acquired', 'Failed', 'Pending'];
    if (!in_array(strtoupper($newStatus), $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid transfer status']);
        exit;
    }

    // Update query
    $stmt = $connextion->prepare("UPDATE Artifact SET transfer_status = ?, updated_date = NOW() WHERE artifactID = ?");
    $stmt->bind_param("si", $newStatus, $artifactID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update the database']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$connextion->close();
?>
