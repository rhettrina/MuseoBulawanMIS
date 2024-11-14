<?php


include "article_db.php";

$order = "DESC"; // Default to newest first
if (isset($_GET['sort']) && $_GET['sort'] === "oldest") {
    $order = "ASC"; // Sort by oldest if specified
}

// Query to fetch articles, ordered by created_at date
$sql = "SELECT id, created_at, article_title, article_type, updated_date FROM articles ORDER BY created_at $order";
$result = $conn->query($sql);

$articles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }
}

// Return the result as JSON
echo json_encode([
    "total" => count($articles),
    "articles" => $articles
]);

$conn->close();
?>
