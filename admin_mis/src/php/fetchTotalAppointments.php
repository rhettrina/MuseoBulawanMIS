<?php
// Include the database connection
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0); 
    }


include('db_connect.php');

// Query to count the total number of articles
$query = "SELECT COUNT(*) AS total_appointments FROM form_data";
$result = mysqli_query($connextion, $query);

// Check if the query was successful
if ($result) {
    $row = mysqli_fetch_assoc($result);
    // Return the count as JSON
    echo json_encode(['total_appointments' => $row['total_appointments']]);
} else {
    echo json_encode(['error' => 'Error fetching data']);
}

// Close the database connection
mysqli_close($connextion);


?>
