<?php
include 'db_connect.php'; // Ensure this file sets up a $connection variable

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $appointmentId = intval($_GET['id']); // The ID parameter passed in the URL

    // Updated SQL query that joins appointment and visitor tables
    $query = "
        SELECT 
            a.appointmentID AS formID, 
            v.name AS visitor_name, 
            a.preferred_time AS appointment_time, 
            a.preferred_date AS appointment_date, 
            a.population_countID AS number_of_attendees,  
            a.status, 
            a.created_at
        FROM 
            appointment AS a
        JOIN 
            visitor AS v ON a.visitorID = v.visitorID
        WHERE 
            a.appointmentID = ?"; // Use the appointment ID to filter the result

    // Prepare the SQL statement
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $appointmentId); // Bind the appointment ID to the query

    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $appointment = $result->fetch_assoc();
            echo json_encode($appointment); // Return the result as JSON
        } else {
            echo json_encode(['error' => 'Appointment not found']);
        }
    } else {
        echo json_encode(['error' => 'Error executing query']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid appointment ID']);
}

$connection->close(); // Close the database connection
?>
