<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'my_database');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sorting logic
$order = isset($_GET['sort']) && $_GET['sort'] === 'date-oldest' ? 'ASC' : 'DESC';

// Fetching data
$sql = "SELECT preferred_date, first_name, last_name, preferred_time, attendees FROM form_data ORDER BY preferred_date $order, preferred_time $order";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
