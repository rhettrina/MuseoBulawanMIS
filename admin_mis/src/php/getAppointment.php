<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header('Content-Type: application/json'); // Set Content-Type early

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0); // Handle preflight
}

include 'db_connect.php';

// Check database connection
if (!$connection) { // Assuming $connection is the correct variable name
    http_response_code(500);
    error_log('Database connection failed: ' . mysqli_connect_error());
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Validate and sanitize appointment ID
$appointmentID = $_GET['id'] ?? null;

if (!filter_var($appointmentID, FILTER_VALIDATE_INT)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid or missing appointment ID']);
    exit;
}

// Prepare the query
$query = "
    SELECT 
        a.appointmentID AS formID, 
        v.name AS visitor_name, 
        v.email AS visitor_email, 
        v.phone AS visitor_phone, 
        v.address AS visitor_address, 
        v.organization AS visitor_organization, 
        a.population_countID AS number_of_attendees,  
        a.preferred_date AS appointment_date, 
        a.preferred_time AS appointment_time, 
        a.notes AS appointment_notes, 
        a.status AS appointment_status, 
        a.confirmation_date AS appointment_confirmation_date, 
        a.created_at AS appointment_created_at
    FROM 
        appointment AS a
    JOIN 
        visitor AS v ON a.visitorID = v.visitorID
    WHERE 
        a.appointmentID = ?
";

$stmt = $connection->prepare($query);
if (!$stmt) {
    http_response_code(500); // Internal Server Error
    error_log('Prepare failed: ' . $connection->error);
    echo json_encode(['error' => 'Failed to prepare the statement']);
    exit;
}

// Bind parameters
if (!$stmt->bind_param("i", $appointmentID)) {
    http_response_code(500); // Internal Server Error
    error_log('Bind failed: ' . $stmt->error);
    echo json_encode(['error' => 'Failed to bind parameters']);
    exit;
}

// Execute the statement
if (!$stmt->execute()) {
    http_response_code(500); // Internal Server Error
    error_log('Execute failed: ' . $stmt->error);
    echo json_encode(['error' => 'Failed to execute the statement']);
    exit;
}

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Appointment not found']);
    exit;
}

$appointment = $result->fetch_assoc();

// Return the appointment as JSON
echo json_encode($appointment);

// Close resources
$stmt->close();
$connection->close();
?>
