<?php
// save_image.php

// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "museum_floor_plans";       // Replace with your database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the POST data
if (isset($_POST['imageData']) && isset($_POST['name'])) {
    // Extract the image data
    $imageData = $_POST['imageData'];
    $name = $_POST['name'];

    // Remove the data URL prefix if present
    $filteredData = explode(',', $imageData);
    if (count($filteredData) > 1) {
        $imageData = $filteredData[1];
    } else {
        $imageData = $filteredData[0];
    }

    // Decode the base64 data
    $decodedImage = base64_decode($imageData);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO floor_plans (name, image_data) VALUES (?, ?)");
    $stmt->bind_param("sb", $name, $null); // 's' for string, 'b' for blob

    // Send the decoded image data as a blob
    $stmt->send_long_data(1, $decodedImage); // 1 is the index of "image_data" in the bind_param

    // Execute the statement
    if ($stmt->execute()) {
        // Success
        echo json_encode(['status' => 'success', 'message' => 'Image saved successfully!']);
    } else {
        // Error
        echo json_encode(['status' => 'error', 'message' => 'Failed to save image.', 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

$conn->close();
?>