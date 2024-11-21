<?php
// Include database connection file
include 'db_connection.php';

// Get donation ID from URL query parameter
$donationId = isset($_GET['id']) ? $_GET['id'] : null;

if ($donationId) {
    // Prepare and execute the DELETE query
    $query = "DELETE FROM donation WHERE id = ?";
    $stmt = $conn->prepare($query);
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

$conn->close();
?>
