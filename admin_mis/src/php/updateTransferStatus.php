<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Respond to OPTIONS pre-flight request
}

// Include database connection
include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['artifactID']) || !isset($input['newStatus']) || !isset($input['formType'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    $artifactID = $input['artifactID'];
    $newStatus = $input['newStatus'];
    $formType = $input['formType']; // Either 'Lending' or 'Donation'

    // Validate input
    $validStatuses = ['Acquired', 'Pending', 'Failed'];
    if (!in_array($newStatus, $validStatuses)) {
        echo json_encode(['success' => false, 'error' => 'Invalid transfer status']);
        exit;
    }

    // Determine table to update based on form type
    $table = '';
    $idColumn = '';
    if ($formType === 'Lending') {
        $table = 'Lending';
        $idColumn = 'lendingID';
    } elseif ($formType === 'Donation') {
        $table = 'Donation';
        $idColumn = 'donationID';
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid form type']);
        exit;
    }

    // Update query
    $stmt = $connextion->prepare("UPDATE $table SET transfer_status = ?, updated_date = NOW() WHERE $idColumn = ?");
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        exit;
    }

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
