<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Include database connection file

include 'db_connect.php';

$id = intval($_GET['id']);
$type = $_GET['type'];

if ($type === 'lending') {
    $query = "SELECT * FROM lending_form WHERE id = ?";
} else {
    $query = "SELECT * FROM donation_form WHERE id = ?";
}

$stmt = $connextion->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode($result->fetch_assoc());
} else {
    echo json_encode(['error' => 'No data found']);
}
$stmt->close();
?>
