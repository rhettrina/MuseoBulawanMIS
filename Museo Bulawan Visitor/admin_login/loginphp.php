<?php
session_start();

$username = $_POST['username'];
$password = $_POST['password'];

// Database connection
$conn = new mysqli('localhost', 'root', '', 'my_database');

// Check connection
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
} else {
    // Prepare the SQL statement to retrieve user with matching credentials
    $stmt = $conn->prepare("SELECT * FROM credentials WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    
    $result = $stmt->get_result();

    // Check if a matching record was found
    if ($result->num_rows > 0) {
        // Save username to session and redirect to dashboard
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
        
        // Redirect back to login page with error flag
        header("Location: login.html?error=1");
        exit();
    }
}
?>
