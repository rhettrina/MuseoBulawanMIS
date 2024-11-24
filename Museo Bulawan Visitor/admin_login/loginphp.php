<?php
session_start();

// Get user input from the POST request
$username = $_POST['username'];
$password = $_POST['password'];

include 'db_connect.php';


// Prepare the SQL statement to retrieve the user with matching credentials
$stmt = $connextion->prepare("SELECT * FROM credentials WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);

// Execute the statement
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Check if a matching record was found
if ($result->num_rows > 0) {
    // Save username to the session and redirect to dashboard
    $_SESSION['username'] = $username;


    $stmt->close();
    $connextion->close();

    header("Location: dashboard.html");
    exit();
} else {
    // Close the statement and connection before redirecting
    $stmt->close();
    $connextion->close();

    // Redirect back to login page with an error flag
    header("Location: login.html?error=1");
    exit();
}
?>
