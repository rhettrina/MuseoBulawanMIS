<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db_connect.php'; // Include the database connection

$response = ["success" => false, "error" => ""]; // Default response

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve and validate inputs
        $id = $_POST["id"] ?? null;
        $action = $_POST["action"] ?? null;

        if (!$id || !$action) {
            throw new Exception("Missing required fields: appointment ID or action.");
        }

        // Determine the status based on the action
        $status = null;
        switch (strtolower(trim($action))) {
            case 'approve':
                $status = 'Approved';
                break;
            case 'reject':
                $status = 'Rejected';
                break;
            default:
                throw new Exception("Invalid action. Allowed actions: approve, reject.");
        }

        // Update query
        $query = "UPDATE appointment SET status = ?, confirmation_date = NOW() WHERE appointmentID = ?";
        $stmt = $connextion->prepare($query);

        if (!$stmt) {
            throw new Exception("Failed to prepare the database query.");
        }

        // Bind and execute the statement
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            $response["success"] = true;
            $response["message"] = "Appointment status updated to $status.";
        } else {
            throw new Exception("Failed to execute the database query.");
        }

        $stmt->close();
    } else {
        throw new Exception("Invalid request method. Only POST is allowed.");
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

// Output the response as JSON
echo json_encode($response);

$connextion->close(); // Close the database connection
?>
