<?php
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $barangay = $_POST['barangay'];
    $street = $_POST['street'];
    $organization = $_POST['organization'];
    $attendees = $_POST['age'];
    $preferred_date = $_POST['preferred_date'];
    $preferred_time = $_POST['time'];
    $notes = $_POST['notes'];

     //database connection
   $conn = new mysqli('localhost','root','','');
   if($conn->connect_error){
    die('connection Failed : '.$conn->connect_error);
   } else {
    $stmt = $conn->prepare("INSERT INTO form_data (first_name, last_name, email, phone, province, city, barangay, street, organization, attendees, preferred_date, preferred_time, notes) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
   } 
   $stmt->bind_param("sssssssssisss", 
$first_name, 
 $last_name, 
    $email, 
    $phone,            
    $province, 
    $city, 
    $barangay, 
    $street, 
    $organization,      
    $attendees,         
    $preferred_date, 
    $preferred_time, 
    $notes
    );

    $stmt->execute();
    echo "Registration Successfully...";
    $stmt->close();
    #conn->close();
?>

