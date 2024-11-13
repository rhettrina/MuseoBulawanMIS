<?php
// db_conn.php

$user = 'root';
$pass = '';
$dbName = 'donation';
$db = new mysqli('localhost', $user, $pass, $dbName);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>
