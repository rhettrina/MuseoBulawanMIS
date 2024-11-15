<?php
session_start();

// Get user input from the POST request
$username = $_POST['username'];
$password = $_POST['password'];

// Database connection
$database_server = "localhost";
$database_user = "u376871621_bomb_squad";
$database_password = "Fujiwara000!";
$database_name = "u376871621_mb_mis";

// Create a connection
$conn = new mysqli($database_server, $database_user, $database_password, $database_name);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL statement to retrieve the user with matching credentials
$stmt = $conn->prepare("SELECT * FROM credentials WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);

// Execute the statement
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Check if a matching record was found
if ($result->num_rows > 0) {
    // Save username to the session and redirect to dashboard
    $_SESSION['username'] = $username;

    // Close the statement and connection before redirecting
    $stmt->close();
    $conn->close();

    header("Location: dashboard.html");
    exit();
} else {
    // Close the statement and connection before redirecting
    $stmt->close();
    $conn->close();

    // Redirect back to login page with an error flag
    header("Location: login.html?error=1");
    exit();
}
?>
