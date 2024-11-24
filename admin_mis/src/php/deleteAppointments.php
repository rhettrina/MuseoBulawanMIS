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
    $connextion->begin_transaction();

    try {
        // 1. Retrieve the visitorID associated with this appointmentID
        $queryVisitorID = "SELECT visitorID FROM appointment WHERE appointmentID = ?";
        $stmtVisitorID = $connextion->prepare($queryVisitorID);
        if (!$stmtVisitorID) {
            throw new Exception('Failed to prepare visitorID retrieval query.');
        }
        $stmtVisitorID->bind_param("i", $appointmentID);
        $stmtVisitorID->execute();
        $resultVisitorID = $stmtVisitorID->get_result();
        if ($resultVisitorID->num_rows > 0) {
            $row = $resultVisitorID->fetch_assoc();
            $visitorID = $row['visitorID'];
        } else {
            throw new Exception('Appointment not found.');
        }

        // 2. Delete related records from other tables that reference appointmentID
        // Adjust table names and column names as per your database schema

        // Example: Delete from 'appointment_details' table
        $deleteDetails = "DELETE FROM appointment WHERE appointmentID = ?";
        $stmtDetails = $connextion->prepare($deleteDetails);
        if (!$stmtDetails) {
            throw new Exception('Failed to prepare appointment_details deletion query.');
        }
        $stmtDetails->bind_param("i", $appointmentID);
        $stmtDetails->execute();

        // Add more deletion queries for other related tables if necessary
        // ...

        // 3. Delete the appointment record from the 'appointment' table
        $deleteAppointment = "DELETE FROM appointment WHERE appointmentID = ?";
        $stmtAppointment = $connextion->prepare($deleteAppointment);
        if (!$stmtAppointment) {
            throw new Exception('Failed to prepare appointment deletion query.');
        }
        $stmtAppointment->bind_param("i", $appointmentID);
        $stmtAppointment->execute();

        // 4. Check if the visitorID is associated with any other appointments
        $queryVisitorAppointments = "SELECT COUNT(*) as count FROM appointment WHERE visitorID = ?";
        $stmtVisitorAppointments = $connextion->prepare($queryVisitorAppointments);
        if (!$stmtVisitorAppointments) {
            throw new Exception('Failed to prepare visitor appointments count query.');
        }
        $stmtVisitorAppointments->bind_param("i", $visitorID);
        $stmtVisitorAppointments->execute();
        $resultVisitorAppointments = $stmtVisitorAppointments->get_result();
        $row = $resultVisitorAppointments->fetch_assoc();
        $appointmentCount = $row['count'];

        // If no other appointments are associated with this visitor, delete the visitor record
        if ($appointmentCount == 0) {
            $deleteVisitor = "DELETE FROM visitor WHERE visitorID = ?";
            $stmtVisitor = $connextion->prepare($deleteVisitor);
            if (!$stmtVisitor) {
                throw new Exception('Failed to prepare visitor deletion query.');
            }
            $stmtVisitor->bind_param("i", $visitorID);
            $stmtVisitor->execute();
        }

        // 5. Commit the transaction
        $connextion->commit();
        echo json_encode(['message' => 'Appointment and associated visitor deleted successfully.']);
    } catch (Exception $e) {
        // Rollback the transaction in case of any errors
        $connextion->rollback();
        http_response_code(500);
        echo json_encode(['error' => 'Deletion failed: ' . $e->getMessage()]);
    }
} else {
    // Error if appointment ID is not provided
    http_response_code(400);
    echo json_encode(['error' => 'Appointment ID is required.']);
}

// Close the database connection
$connextion->close();
?>
