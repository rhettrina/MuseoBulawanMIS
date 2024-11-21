<?php

// Set the CORS headers to allow cross-origin requests from all origins
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header('Content-Type: application/json');

// Handle the preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // If it's a preflight request, just return a 200 response without any further processing
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and decode the JSON input
    $postData = json_decode(file_get_contents('php://input'), true);

    if (isset($postData['action']) && $postData['action'] === 'fetch_articles') {
        // Include the database connection file
        include('db_connection.php');

        // Ensure the database connection is established
        if (!$connection) {
            echo json_encode(['error' => 'Database connection failed.']);
            exit;
        }

        // Define base URL for images
        $imageBaseUrl = "https://lightpink-dogfish-795437.hostingersite.com/admin_mis/src/uploads/articlesUploads/";

        // SQL query to fetch articles
        $sql = "SELECT id, article_title, article_type, location, author, imgu1, imgu1_details, 
                p1box_left, p1box_right, imgu2, imgu3, p2box, p3box, created_at, updated_date 
                FROM articles";

        // Use prepared statement to execute query
        if ($stmt = $connection->prepare($sql)) {
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if query execution was successful and results were returned
            if ($result) {
                $articles = [];
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $articles[] = [
                            'id' => $row['id'],
                            'article_title' => $row['article_title'],
                            'article_type' => $row['article_type'],
                            'location' => $row['location'],
                            'author' => $row['author'],
                            'imgu1' => $imageBaseUrl . $row['imgu1'],  // Full URL to image
                            'imgu1_details' => $row['imgu1_details'],
                            'p1box_left' => $row['p1box_left'],
                            'p1box_right' => $row['p1box_right'],
                            'imgu2' => $imageBaseUrl . $row['imgu2'],
                            'imgu3' => $imageBaseUrl . $row['imgu3'],
                            'p2box' => $row['p2box'],
                            'p3box' => $row['p3box'],
                            'created_at' => $row['created_at'],
                            'updated_date' => $row['updated_date'],
                        ];
                    }
                    echo json_encode($articles);
                } else {
                    echo json_encode(['error' => 'No articles found.']);
                }
            } else {
                echo json_encode(['error' => 'Error executing query: ' . $connection->error]);
            }

            // Close the statement
            $stmt->close();
        } else {
            echo json_encode(['error' => 'Error preparing the query: ' . $connection->error]);
        }

        // Close the database connection
        $connection->close();
    } else {
        echo json_encode(['error' => 'Invalid action.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
