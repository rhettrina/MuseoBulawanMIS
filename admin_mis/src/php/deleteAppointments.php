<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include 'db_connect.php';

$appointmentId = isset($_GET['id']) ? $_GET['id'] : null;

if ($appointmentId) {
    $query = "DELETE FROM appointment WHERE appointmentID = ?";
    $stmt = $connection->prepare($query); // Correct variable name is $connection, not $connextion
    $stmt->bind_param("i", $appointmentId); // Bind the appointment ID

    if ($stmt->execute()) {
        // Return success message
        echo json_encode(['message' => 'Appointment deleted successfully']);
    } else {
        // Return error message
        echo json_encode(['error' => 'Failed to delete appointment']);
    }

    $stmt->close();
} else {
    // Return error if ID is not provided
    echo json_encode(['error' => 'Invalid appointment ID']);
}

$connection->close(); // Close connection using $connection
?>
