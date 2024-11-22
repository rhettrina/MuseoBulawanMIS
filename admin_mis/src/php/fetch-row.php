<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database connection file
include 'db_connect.php';

// Get 'id' and 'type' from URL parameters
$id = intval($_GET['id']);
$type = $_GET['type'];

// Debugging: Check the received parameters
echo "ID: " . $id . "<br>";
echo "Type: " . $type . "<br>";

// Check if type is valid
if ($type === 'Lending') {
    $query = "SELECT * FROM lending_form WHERE id = ?";
} else if ($type === 'Donation') {
    $query = "SELECT * FROM donation_form WHERE id = ?";
} else {
    echo json_encode(['error' => 'Invalid type']);
    exit;
}

// Prepare the SQL query
$stmt = $connextion->prepare($query);

// Check for errors in query preparation
if ($stmt === false) {
    die('MySQL prepare error: ' . $connextion->error);
}

// Bind the parameter (id)
$stmt->bind_param("i", $id);

// Execute the query
if (!$stmt->execute()) {
    die('MySQL execute error: ' . $stmt->error);
}

// Get the result
$result = $stmt->get_result();

// Check if any rows are returned
if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'No data found']);
}

// Close the prepared statement
$stmt->close();
?>
