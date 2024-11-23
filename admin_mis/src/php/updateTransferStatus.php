<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, UPDATE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Respond to OPTIONS pre-flight request
}

// Include database connection
require_once('db_connect.php');

// Ensure no extra output
ob_start();

// Capture incoming request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['artifactID']) && isset($data['transfer_status'])) {
    $artifactID = $data['artifactID'];
    $transferStatus = $data['transfer_status'];

    // Prepare and execute the SQL query
    $sql = "UPDATE donations SET transfer_status = ? WHERE artifactID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $transferStatus, $artifactID);

    if ($stmt->execute()) {
        echo "success"; // Return a plain text success message
    } else {
        error_log("Database error: " . $stmt->error);
        echo "error: Database update failed"; // Return a plain text error message
    }

    $stmt->close();
} else {
    error_log("Invalid data received: " . print_r($data, true));
    echo "error: Invalid input data"; // Return an error message for invalid input
}

// Ensure clean output
$content = ob_get_clean();
echo trim($content);
$conn->close();
?>
