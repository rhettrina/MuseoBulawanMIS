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
$preferred_date = $_POST['preferred_date'];  // Ensure this is in YYYY-MM-DD format
$preferred_time = $_POST['time'];  // Ensure this is in HH:MM:SS format
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

}

// Insert into appointment table
$stmt = $connextion->prepare("INSERT INTO appointment (visitorID, preferred_time, preferred_date, population_countID, appointment_dateID) VALUES (?, ?, ?, ?, ?)");

if ($stmt) {
    // Ensure that the preferred_date is in YYYY-MM-DD format and preferred_time is in HH:MM:SS format
    $population_countID = $attendees; // Assuming the attendees count is stored in population_countID
    $appointment_dateID = $preferred_date; // Assuming appointment_dateID is the same as preferred_date for now (adjust as needed)
    
    // Bind the parameters with the appropriate types
    $stmt->bind_param("ssiii", $visitor_id, $preferred_time, $preferred_date, $population_countID, $appointment_dateID);

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id; // Capture the generated appointment ID
        
        // Success message and redirect to appointmentindex.html
        echo "<script>alert('Appointment successfully scheduled!'); window.location.href='appointindex.html';</script>";
    } else {
        die("Error inserting into appointment table: " . $stmt->error);
    }
    $stmt->close();
}

// Close the connection
$connextion->close();
?>
