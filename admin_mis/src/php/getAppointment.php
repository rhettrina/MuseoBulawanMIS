<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); // Handle preflight
}

include 'db_connect.php';

if (!$connextion) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$appointmentID = $_GET['id'] ?? null;

if (!$appointmentID) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Missing appointment ID']);
    exit;
}

// Query to fetch a single appointment
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

$stmt = $connextion->prepare($query);
$stmt->bind_param("i", $appointmentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404); // Not Found
    echo json_encode(['error' => 'Appointment not found']);
    exit;
}

$appointment = $result->fetch_assoc();

// Return the appointment as JSON
header('Content-Type: application/json');
echo json_encode($appointment);
?>
