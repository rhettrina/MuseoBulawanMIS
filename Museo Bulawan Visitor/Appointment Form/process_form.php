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
$attendees = (isset($_POST['age']) && is_numeric($_POST['age']) && $_POST['age'] > 0) ? intval($_POST['age']) : null; // Attendees count
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
$stmt = $connextion->prepare("INSERT INTO appointment (visitor_id, preferred_time, preferred_date, population_count) VALUES (?, ?, ?, ?)");
if ($stmt) {
    $stmt->bind_param("issi", $visitor_id, $preferred_time, $preferred_date, $attendees);

    if ($stmt->execute()) {
        $appointment_id = $stmt->insert_id; // Capture the generated appointment ID
    } else {
        die("Error inserting into appointment table: " . $stmt->error);
    }
    $stmt->close();
}

// Insert into attendance table
$stmt = $connextion->prepare("INSERT INTO attendance (attendanceID, number_of_present, appointment_dateID, appointmentID, visitorID) VALUES (?, ?, ?, ?, ?)");
if ($stmt) {
    // Prepare other data for the attendance table
    $attendance_id = null; // Assume auto-incremented if set in the database
    $appointment_date_id = null; // Provide correct value or logic if necessary
    
    $stmt->bind_param("iiiii", $attendance_id, $attendees, $appointment_date_id, $appointment_id, $visitor_id);

    if ($stmt->execute()) {
        echo "Appointment and attendance records successfully created!";
        header("Location: appointindex.html");
        exit;
    } else {
        die("Error inserting into attendance table: " . $stmt->error);
    }
    $stmt->close();
} else {
    die("Error preparing statement: " . $connextion->error);
}

// Close the connection
$connextion->close();
?>
