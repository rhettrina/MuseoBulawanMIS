
<?php
    header("Access-Control-Allow-Origin: *"); 
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
    header("Access-Control-Allow-Headers: Content-Type, x-requested-with"); 


    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0); 
    }

    $servername = "localhost"; 
    $username = "root";       
    $password = "";            
    $dbname = "museo_bulawan";   

    // Create connection
    $connextion = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($connextion->connect_error) {
        die("Connection failed: " . $connextion->connect_error);
    } else {
        echo "Connected successfully to the database!";
    };
?>
