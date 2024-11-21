<?php

include "article_db.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON input
    $input = json_decode(file_get_contents('php://input'), true);

    // Get the article ID from JSON data
    if (isset($input['id'])) {
        $articleId = intval($input['id']);

        // Prepare the DELETE SQL statement
        $sql = "DELETE FROM articles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $articleId);

        // Execute and check if deletion was successful
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(["message" => "Article deleted successfully"]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Failed to delete article"]);
        }
        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid article ID"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}

$conn->close();
?>
