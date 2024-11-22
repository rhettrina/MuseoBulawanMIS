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

// Create connection
$connextion = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connextion->connect_error) {
    die("Connection failed: " . $connextion->connect_error);
}

// Collect form data
$first_name = $_POST['firstName'];
$last_name = $_POST['lastName'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$province = $_POST['province'];
$city = $_POST['city'];
$barangay = $_POST['barangay'];
$street = $_POST['street'];
$organization = $_POST['organization'];
$attendees = $_POST['age'];
$preferred_date = $_POST['preferred_date'];
$preferred_time = $_POST['time'];
$notes = $_POST['notes'];

// Prepare and bind the SQL statement
$stmt = $connextion->prepare("INSERT INTO form_data 
    (first_name, last_name, email, phone, province, city, barangay, street, organization, attendees, preferred_date, preferred_time, notes) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if ($stmt) {
    $stmt->bind_param(
        "sssssssssisss",
        $first_name,
        $last_name,
        $email,
        $phone,
        $province,
        $city,
        $barangay,
        $street,
        $organization,
        $attendees,
        $preferred_date,
        $preferred_time,
        $notes
    );
    
    if ($conn->query($appointment_sql) === TRUE) {
        echo "Appointment submitted successfully!";
        header("Location: appointmentindex.html");
        exit; // Ensure no further code runs after the redirect
    } else {
        echo "Error inserting into appointments table: " . $conn->error;
    }
    
    

    $stmt->close();
} else {
    echo "Error preparing statement: " . $connextion->error;
}



// Close the connection
$connextion->close();
?>
