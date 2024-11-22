<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

include 'db_conn.php'; // Include your database connection

$formType = $_GET['type']; // 'donation' or 'lending'
$id = $_GET['id'];

// Set the appropriate SQL query based on the form type
if ($formType == 'donation') {
    $sql = "SELECT `id`, `first_name`, `last_name`, `artifact_title`, `submission_date`, `status`, `transfer_status`
            FROM `donation_form`
            WHERE `id` = ?";
} else if ($formType == 'lending') {
    $sql = "SELECT `id`, `first_name`, `last_name`, `artifact_title`, `submitted_at`, `status`, `transfer_status`
            FROM `lending_form`
            WHERE `id` = ?";
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid form type']);
    exit;
}

// Prepare and execute the query
$stmt = $connextion->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'row' => $row]);
} else {
    echo json_encode(['success' => false, 'error' => 'No data found']);
}

$stmt->close();
$connextion->close();
?>
