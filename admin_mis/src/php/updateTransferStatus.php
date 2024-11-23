<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Respond to OPTIONS pre-flight request
}

include 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve input from $_POST
    $artifactID = $_POST['artifactID'] ?? null;
    $newStatus = $_POST['newStatus'] ?? null;
    $formType = $_POST['formType'] ?? null;

    error_log("Received input: artifactID=$artifactID, newStatus=$newStatus, formType=$formType"); // Debugging

    if (!$artifactID || !$newStatus || !$formType) {
        error_log("Invalid input detected");
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit;
    }

    // Validate input
    $validStatuses = ['Acquired', 'Pending', 'Failed'];
    if (!in_array($newStatus, $validStatuses)) {
        error_log("Invalid status: $newStatus");
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
        error_log("Invalid form type: $formType");
        echo json_encode(['success' => false, 'error' => 'Invalid form type']);
        exit;
    }

    // Update query
    $stmt = $connextion->prepare("UPDATE $table SET transfer_status = ?, updated_date = NOW() WHERE $idColumn = ?");
    if (!$stmt) {
        error_log("Failed to prepare statement");
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        exit;
    }

    $stmt->bind_param("si", $newStatus, $artifactID);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        error_log("Database update failed");
        echo json_encode(['success' => false, 'error' => 'Failed to update the database']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$connextion->close();
?>
