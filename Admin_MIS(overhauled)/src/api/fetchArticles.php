<?php

include_once "mbConnection.php";

if ($connection->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $connection->connect_error]));
}


$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at'; 
$sortOrder = isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'desc' ? 'DESC' : 'ASC'; 


$allowedColumns = ['id', 'article_title', 'author', 'created_at', 'updated_date', 'popularity'];
if (!in_array($sortBy, $allowedColumns)) {
    $sortBy = 'created_at'; 
}


$sortBy = $connection->real_escape_string($sortBy);
$sortOrder = $connection->real_escape_string($sortOrder);


$sql = "SELECT * FROM articles ORDER BY $sortBy $sortOrder";


$result = $connection->query($sql);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

if (!$result) {

    echo json_encode([
        'success' => false,
        'message' => 'Query failed: ' . $connection->error
    ]);
    $connection->close();
    exit();
}


$totalCountQuery = "SELECT COUNT(*) as total FROM articles";
$countResult = $connection->query($totalCountQuery);
$totalCount = 0;

if ($countResult && $countResult->num_rows > 0) {
    $countRow = $countResult->fetch_assoc();
    $totalCount = (int) $countRow['total'];
}

if ($result->num_rows > 0) {
    $articles = [];
    while ($row = $result->fetch_assoc()) {
        $articles[] = $row;
    }


    echo json_encode([
        'success' => true,
        'data' => $articles,
        'total_count' => $totalCount
    ], JSON_PRETTY_PRINT);
} else {

    echo json_encode([
        'success' => false,
        'message' => '0',
        'data' => [],
        'total_count' => $totalCount
    ]);
}


$connection->close();

?>
