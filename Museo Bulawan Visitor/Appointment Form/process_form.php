<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database connection
$servername = "localhost";
$username = "u376871621_bomb_squad";
$password = "Fujiwara000!";
$dbname = "u376871621_mb_mis";

$connextion = new mysqli($servername, $username, $password, $dbname);

if ($connextion->connect_error) {
    die("Connection failed: " . $connextion->connect_error);
}

// Collect and sanitize user input
$first_name = $_POST['firstName'];
$last_name = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$province = $_POST['province'];
$city = $_POST['city'];
$barangay = $_POST['barangay'];
$street = $_POST['street'];
$organization = isset($_POST['organization']) ? htmlspecialchars($_POST['organization']) : null; // Organization field
$attendees = isset($_POST['attendees_count']) && is_numeric($_POST['attendees_count']) && $_POST['attendees_count'] > 0 ? intval($_POST['attendees_count']) : null; // Attendees count
$preferred_date = $_POST['preferred_date'];
$preferred_time = $_POST['time'];
$notes = $_POST['notes'];

// Insert into visitor table
$stmt = $connextion->prepare("INSERT INTO visitor (name, email, phone, address, organization) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    $full_name = $first_name . " " . $last_name;
    $address = trim("$street, $barangay, $city, $province");
    $stmt->bind_param("sssss", $full_name, $email, $phone, $address, $organization);

    if ($stmt->execute()) {
        $visitor_id = $stmt->insert_id; // Capture the generated visitor ID
    } else {
        die("Error inserting into visitor table: " . $stmt->error);
    }
    $stmt->close();
}

// Insert into appointment table
$stmt = $connextion->prepare("INSERT INTO appointment (visitorID, preferred_time, preferred_date, population_countID, appointment_dateID) VALUES (?, ?, ?, ?, ?)");

if ($stmt) {
    // Make sure to pass the correct variable names for the parameters
    $population_countID = $attendees; // Assuming the attendees count is stored in population_countID
    $appointment_dateID = $preferred_date; // Assuming appointment_dateID is same as preferred_date for now (adjust as needed)
    
    // Bind the parameters with the appropriate types
    $stmt->bind_param("ssiii", $visitor_id, $preferred_time, $preferred_date, $population_countID, $appointment_dateID);

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id; // Capture the generated appointment ID
    } else {
        die("Error inserting into appointment table: " . $stmt->error);
    }
    $stmt->close();
}

// Close the connection
$connextion->close();
?>
