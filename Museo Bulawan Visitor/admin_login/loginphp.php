<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");


if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}


$servername = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";   

$connextion = new mysqli($servername, $username, $password, $dbname);

if ($connextion->connect_error) {
    die("Connection failed: " . $connextion->connect_error);
}
session_start();


// Set the header to return JSON data
header('Content-Type: application/json');

// Get the raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Check if data is valid JSON
if (!is_array($data)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

$username = isset($data['username']) ? $data['username'] : '';
$password = isset($data['password']) ? $data['password'] : '';


if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required.']);
    exit();
}



$stmt = $connextion->prepare("SELECT * FROM credentials WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $username);

// Execute the statement
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Check if a matching record was found
if ($result->num_rows > 0) {
    // Fetch the user data
    $user = $result->fetch_assoc();

    // Verify the password
    if (password_verify($password, $user['password'])) {
        // Password is correct
        $_SESSION['username'] = $username;
        $_SESSION['admin_logged_in'] = true;

        echo json_encode(['success' => true, 'message' => 'Login successful.']);
    } else {
        // Password is incorrect
        echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
    }
} else {
    // No user found with that username
    echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
}

// Close the statement and connection
$stmt->close();
$connextion->close();
?>
