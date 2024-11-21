<?php
header("Access-Control-Allow-Origin: *");  // Allow all origins or change to your domain (e.g., http://127.0.0.1:5501)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");  // Allow specific methods
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");  // Allow the 'x-requested-with' header




if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $postData = json_decode(file_get_contents('php://input'), true);
    if (isset($postData['action']) && $postData['action'] === 'fetch_articles') {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        include('db_connection.php');


        header('Content-Type: application/json');

        $sql = "SELECT id, article_title, article_type, location, author, imgu1, imgu1_details, p1box_left, p1box_right, imgu2, imgu3, p2box, p3box, created_at, updated_date FROM articles";
        $result = $connection->query($sql);

        $articles = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $articles[] = [
                    'id' => $row['id'],
                    'article_title' => $row['article_title'],
                    'article_type' => $row['article_type'],
                    'location' => $row['location'],
                    'author' => $row['author'],
                    'imgu1' => $row['imgu1'],
                    'imgu1_details' => $row['imgu1_details'],
                    'p1box_left' => $row['p1box_left'],
                    'p1box_right' => $row['p1box_right'],
                    'imgu2' => $row['imgu2'],
                    'imgu3' => $row['imgu3'],
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

        $connection->close();
    } else {
        echo json_encode(['error' => 'Invalid action.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
?>
