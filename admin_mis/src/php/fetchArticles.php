<?php
// Include your database connection
include 'db_connect.php';

$sort = $_GET['sort'] ?? 'newest'; // Default sort is 'newest'
$order = ($sort === 'oldest') ? 'ASC' : 'DESC';

// Query to fetch sorted articles
$query = "SELECT id, article_title, article_type, created_at, 
                 IFNULL(updated_date, 'Not Edited') AS updated_date 
          FROM articles 
          ORDER BY created_at $order";

$result = mysqli_query($connextion, $query);

if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit;
}

$articles = [];
while ($row = mysqli_fetch_assoc($result)) {
    $articles[] = $row;
}

// Return JSON response
echo json_encode($articles);
?>
