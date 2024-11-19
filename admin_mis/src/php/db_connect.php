
<?php
    header("Access-Control-Allow-Origin: *"); 
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
    header("Access-Control-Allow-Headers: Content-Type, x-requested-with"); 


    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0); 
    }
    
    $servername = "localhost"; 
    $username = "u376871621_bomb_squad";       
    $password = "Fujiwara000!";            
    $dbname = "u376871621_mb_mis";   

    // Create connection
    $connextion = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($connextion->connect_error) {
        die("Connection failed: " . $connextion->connect_error);
    }
?>
