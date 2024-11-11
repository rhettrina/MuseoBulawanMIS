<?php 
    header("Access-Control-Allow-Origin: *"); 
    header("Content-Type: application/json");

    include 'article_dbconnect.php';

    $query = 'SELECT COUNT(*) AS total_contents FROM articles';
    $result = $mysqli->query($query);

    if ($result) {
        $total = $result->fetch_assoc()['total_contents'];
        echo json_encode(['total' => $total]);
    } else {
        echo json_encode(['total' => 0, 'error' => 'Query failed']);
    }

    $mysqli->close(); 
?>
