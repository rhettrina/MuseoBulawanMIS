<?php
header("Access-Control-Allow-Origin: *"); // Allows requests from any origin
header("Content-Type: application/json");


ob_start(); // Start output buffering
header('Content-Type: application/json'); // Set content type to JSON
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include 'article_dbconnect.php';

$query = "SELECT id, created_at, article_title, article_type, updated_date FROM articles";
$result = $mysqli->query($query);

$articles = [];
while ($row = $result->fetch_assoc()) {
    $articles[] = $row;
}


echo json_encode($articles);

$result->free();
$mysqli->close();
ob_end_flush(); // End output buffering
?>
