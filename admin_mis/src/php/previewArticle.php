<?php
include 'db_connect.php'; // Ensure this file sets up a $connection variable

header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $articleId = intval($_GET['id']);
    $query = "SELECT * FROM articles WHERE id = ?";
    $stmt = $connextion->prepare($query);
    $stmt->bind_param("i", $articleId);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $article = $result->fetch_assoc();
            echo json_encode($article);
        } else {
            echo json_encode(['error' => 'Article not found']);
        }
    } else {
        echo json_encode(['error' => 'Error executing query']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid article ID']);
}

$connextion->close();
?>