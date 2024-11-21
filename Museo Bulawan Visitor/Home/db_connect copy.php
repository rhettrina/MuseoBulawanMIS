
<?php
// Move this to the top of the PHP file to prevent "Headers already sent" error.
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Ensure no output before headers
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0); 
}

// Database connection
$servername = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";   

$connextion = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($connextion->connect_error) {
    die("Connection failed: " . $connextion->connect_error);
}
?>
