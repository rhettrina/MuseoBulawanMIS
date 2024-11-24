<?php
// Include the database connection
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}


$svn = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";   

$conn = new mysqli($svn, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Initialize an array to store results with default values
$results = [
    'total_donations' => 0,
    'accepted_donations' => 0,
    'rejected_donations' => 0,
    'total_donation_forms' => 0,
    'total_lending_forms' => 0,
    'error' => null
];

// Query to count total donations
$query = "SELECT COUNT(*) AS total FROM Artifact";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_donations'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch total donations: ' . mysqli_error($conn);
}

// Query to count accepted donations
$query = "SELECT COUNT(*) AS accepted FROM Artifact WHERE status = 'Accepted'";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['accepted_donations'] = (int)$row['accepted'];
} else {
    $results['error'] = 'Failed to fetch accepted donations: ' . mysqli_error($conn);
}

// Query to count rejected donations
$query = "SELECT COUNT(*) AS rejected FROM Artifact WHERE status = 'Rejected'";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['rejected_donations'] = (int)$row['rejected'];
} else {
    $results['error'] = 'Failed to fetch rejected donations: ' . mysqli_error($conn);
}

// Query to count total donation forms
$query = "SELECT COUNT(*) AS total FROM Donation";
$result = mysqli_query($conn, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_donation_forms'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch donation forms: ' . mysqli_error($conn);
}

// Query to count total lending forms
$query = "SELECT COUNT(*) AS total FROM Lending";
$result = mysqli_query($connextion, $query);
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $results['total_lending_forms'] = (int)$row['total'];
} else {
    $results['error'] = 'Failed to fetch lending forms: ' . mysqli_error($conn);
}

// Return the results as JSON
echo json_encode($results);

// Close the database connection
mysqli_close($conn);


?>
