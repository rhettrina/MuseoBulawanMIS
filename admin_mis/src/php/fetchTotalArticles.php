<?php
// Include the database connection
include('db_connect.php');

// Query to count the total number of articles
$query = "SELECT COUNT(*) AS total_articles FROM articles";
$result = mysqli_query($connextion, $query);

// Check if the query was successful
if ($result) {
    $row = mysqli_fetch_assoc($result);
    // Return the count as JSON
    echo json_encode(['total_articles' => $row['total_articles']]);
} else {
    echo json_encode(['error' => 'Error fetching data']);
}

// Close the database connection
mysqli_close($connextion);
?>
