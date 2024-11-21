<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, UPDATE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

// Database connection
include 'db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id'], $data['transfer_status'])) {
    $id = $data['id'];
    $transfer_status = $data['transfer_status'];

    // Update query
    $stmt = $connextion->prepare("UPDATE donations SET transfer_status = ?, updated_date = NOW() WHERE id = ?");
    $stmt->bind_param("si", $transfer_status, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data received']);
}

$connextion->close();
?>
