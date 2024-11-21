<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); // Added DELETE here
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

// Include database connection file
include 'db_connect.php';

// Get donation ID from URL query parameter
$donationId = isset($_GET['id']) ? $_GET['id'] : null;

if ($donationId) {
    // Prepare and execute the DELETE query
    $query = "DELETE FROM donation WHERE id = ?";
    $stmt = $connextion->prepare($query); // Use $connextion instead of $conn
    $stmt->bind_param("i", $donationId);

    if ($stmt->execute()) {
        // Return success message
        echo json_encode(['message' => 'Donation deleted successfully']);
    } else {
        // Return error message
        echo json_encode(['error' => 'Failed to delete donation']);
    }

    $stmt->close();
} else {
    // Return error if ID is not provided
    echo json_encode(['error' => 'Invalid donation ID']);
}

$connextion->close(); // Close connection using $connextion
?>
