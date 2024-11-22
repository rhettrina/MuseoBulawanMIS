<?php

include('db_connect.php');

// Sorting logic
$order = isset($_GET['sort']) && $_GET['sort'] === 'date-oldest' ? 'ASC' : 'DESC';

// Fetching data
$sql = "SELECT preferred_date, first_name, last_name, preferred_time, attendees FROM form_data ORDER BY preferred_date $order, preferred_time $order";
$result = $$connextion->close();
->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$connextion->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
