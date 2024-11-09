<?php
$database_server = "localhost";
$database_user = "root";
$database_password = "";
$database_name = "museo_bulawan";
$conn = "";

$conn = mysqli_connect($database_server, $database_user, $database_password, $database_name);


if($conn){
    echo"connection established";

}
else{
    echo"fail";
}
?>