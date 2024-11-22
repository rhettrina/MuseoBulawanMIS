<?php
// Get the HTTP method

include 'db_connect.php';
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'DELETE') {
    // Parse the URL to get the 'id' parameter
    parse_str($_SERVER['QUERY_STRING'], $queryParams);
    if (isset($queryParams['id'])) {
        $articleId = intval($queryParams['id']);

        // Prepare and execute the DELETE statement
        $stmt = $connextion->prepare("DELETE FROM articles WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $articleId);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Article not found.']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Error executing query: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Error preparing statement: ' . $connextion->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No article ID provided.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}

$connextion->close();
?>