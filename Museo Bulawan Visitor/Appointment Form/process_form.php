<?php
// Enable CORS (if required)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database connection parameters
$servername = "localhost";
$username = "u376871621_bomb_squad";
$password = "Fujiwara000!";
$dbname = "u376871621_mb_mis";

// Create a new mysqli connection
$connection = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Collect and sanitize user input
$first_name = sanitize_input($_POST['firstName']);
$last_name = sanitize_input($_POST['lastName']);
$email = sanitize_input($_POST['email']);
$phone = sanitize_input($_POST['phone']);
$province = sanitize_input($_POST['province']);
$city = sanitize_input($_POST['city']);
$barangay = sanitize_input($_POST['barangay']);
$street = sanitize_input($_POST['street']);
$organization = isset($_POST['organization']) ? sanitize_input($_POST['organization']) : null; // Organization field
$attendees = isset($_POST['attendees_count']) && is_numeric($_POST['attendees_count']) && $_POST['attendees_count'] > 0 ? intval($_POST['attendees_count']) : null; // Attendees count
$preferred_date = sanitize_input($_POST['preferred_date']);
$preferred_time = sanitize_input($_POST['time']);
$notes = isset($_POST['notes']) ? sanitize_input($_POST['notes']) : null;
$purpose = sanitize_input($_POST['purpose']);
// Validate preferred_date format (YYYY-MM-DD)
if (DateTime::createFromFormat('Y-m-d', $preferred_date) === false) {
    die('Invalid date format. Please use YYYY-MM-DD.');
}

// Check required fields
if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || empty($province) || empty($city) || empty($barangay) || empty($preferred_date) || empty($preferred_time) || empty($purpose) || empty($attendees)) {
    die("Please fill in all required fields.");
}

// Insert into visitor table using prepared statements
$visitor_stmt = $connection->prepare("INSERT INTO visitor (name, email, phone, address, organization) VALUES (?, ?, ?, ?, ?)");
if ($visitor_stmt) {
    $full_name = $first_name . " " . $last_name;
    $address = trim("$street, $barangay, $city, $province");
    $visitor_stmt->bind_param("sssss", $full_name, $email, $phone, $address, $organization);

    if ($visitor_stmt->execute()) {
        $visitor_id = $visitor_stmt->insert_id; // Get the inserted visitor ID
    } else {
        die("Error inserting into visitor table: " . $visitor_stmt->error);
    }

    $visitor_stmt->close();
} else {
    die("Error preparing visitor insertion statement: " . $connection->error);
}

// Insert into appointment table using prepared statements
$appointment_stmt = $connection->prepare("INSERT INTO appointment (visitorID, preferred_time, preferred_date, population_countID, notes, purpose) VALUES (?, ?, ?, ?, ?, ?)");

if ($appointment_stmt) {
  
    $appointment_stmt->bind_param("ississ", $visitor_id, $preferred_time, $preferred_date, $attendees, $notes, $purpose);

    if ($appointment_stmt->execute()) {
        $appointment_id = $appointment_stmt->insert_id; // Get the inserted appointment ID

        // Success message and redirect (adjust as needed)
        echo "<script>alert('Appointment successfully scheduled!'); window.location.href='appointindex.html';</script>";
    } else {
        die("Error inserting into appointment table: " . $appointment_stmt->error);
    }

    $appointment_stmt->close();
} else {
    die("Error preparing appointment insertion statement: " . $connection->error);
}

// Close the database connection
$connection->close();
?>
