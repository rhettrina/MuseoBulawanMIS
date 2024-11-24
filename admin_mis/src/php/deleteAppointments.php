<?php
// Set CORS headers to allow cross-origin requests (adjust the origin as needed)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Handle preflight requests (OPTIONS method)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Include the database connection file
include 'db_connect.php';

// Get the appointment ID from the URL query parameter
$appointmentID = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($appointmentID) {
    // Start a transaction
    $connection->begin_transaction();

    try {
        // Delete related records from other tables that reference appointmentID
        // Adjust table names and column names as per your database schema

        // Example: Delete from 'appointment_details' table
        $deleteDetails = "DELETE FROM appointment_details WHERE appointmentID = ?";
        $stmtDetails = $connection->prepare($deleteDetails);
        if (!$stmtDetails) {
            throw new Exception('Failed to prepare appointment_details deletion query.');
        }
        $stmtDetails->bind_param("i", $appointmentID);
        $stmtDetails->execute();

        // Add more deletion queries for other related tables if necessary
        // ...

        // Delete the appointment record from the 'appointment' table
        $deleteAppointment = "DELETE FROM appointment WHERE appointmentID = ?";
        $stmtAppointment = $connection->prepare($deleteAppointment);
        if (!$stmtAppointment) {
            throw new Exception('Failed to prepare appointment deletion query.');
        }
        $stmtAppointment->bind_param("i", $appointmentID);
        $stmtAppointment->execute();

        // Commit the transaction
        $connection->commit();
        echo json_encode(['message' => 'Appointment and related records deleted successfully.']);
    } catch (Exception $e) {
        // Rollback the transaction in case of any errors
        $connection->rollback();
        echo json_encode(['error' => 'Deletion failed: ' . $e->getMessage()]);
    }
} else {
    // Error if appointment ID is not provided
    echo json_encode(['error' => 'Appointment ID is required.']);
}

// Close the database connection
$connection->close();
?>
