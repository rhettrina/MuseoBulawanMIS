<?php
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");

// Database configuration
$servername = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $age = intval($_POST['age']);
    $sex = $conn->real_escape_string($_POST['sex']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $organization = $conn->real_escape_string($_POST['organization']);
    $province = $conn->real_escape_string($_POST['province']);
    $city = $conn->real_escape_string($_POST['city']);
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $street = $conn->real_escape_string($_POST['street']);

    // Collect additional form data specific to lending
    $startingDate = $conn->real_escape_string($_POST['startingDate']);
    $endingDate = $conn->real_escape_string($_POST['endingDate']);
    $displayConditions = $conn->real_escape_string($_POST['displayConditions']);
    $liabilityConcerns = $conn->real_escape_string($_POST['liabilityConcerns']);
    $lendingReason = $conn->real_escape_string($_POST['lendingReason']);

    $artifactTitle = $conn->real_escape_string($_POST['artifactTitle']);
    $artifactDescription = $conn->real_escape_string($_POST['artifactDescription']);
    $acquisition = $conn->real_escape_string($_POST['acquisition']);
    $additionalInfo = $conn->real_escape_string($_POST['additionalInfo']);
    $narrative = $conn->real_escape_string($_POST['narrative']);

    // Initialize image file paths
    $art_img_upload_path = '';
    $doc_img_upload_path = '';
    $rel_img_upload_path = '';

    // Image upload handling
    $allowed_exs = array("jpg", "jpeg", "png");

    // Handle artifact image upload
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_name = $_FILES['artifact_img']['name'];
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                $em = "Sorry, the artifact image is too large.";
                header("Location: lendindex.html?error=$em");
                exit();
            } else {
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    $new_art_img_name = uniqid("IMG-", true) . '.' . $art_img_ex_lc;
                    $art_img_upload_path = 'uploads/artifacts/' . $new_art_img_name;
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);
                } else {
                    $em = "You can't upload files of this type for artifact image.";
                    header("Location: lendindex.html?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the artifact image.";
            header("Location: lendindex.html?error=$em");
            exit();
        }
    }

    // Insert query for the Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_donatorTB);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    // Execute the insert query for Donator
    if ($stmt->execute()) {
        echo "Donator added successfully!<br>";
    } else {
        echo "Error inserting donator: " . $stmt->error;
        exit();
    }

    // Now retrieve the donatorID from the Donator table
    $fk_selector = "SELECT `donatorID` FROM `Donator` WHERE `first_name` = ? AND `last_name` = ? AND `email` = ?";
    $stmt = $conn->prepare($fk_selector);
    $stmt->bind_param("sss", $firstName, $lastName, $email);

    // Execute the query to get donatorID
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Fetch the donatorID from the result
    if ($row = $result->fetch_assoc()) {
        $donatorID = $row['donatorID'];
        echo "Donator ID: " . $donatorID . "<br>";
    } else {
        echo "No matching donator found.";
        exit();
    }

    // Insert query for the Lending table
    $abt_art = "INSERT INTO `Lending`(`donatorID`, `artifact_nameID`, `starting_date`, `ending_date`,  
                 `display_conditions`, `liability_concerns`, `lending_reason`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($abt_art);
    $stmt->bind_param("issssss", $donatorID, $artifactTitle, $startingDate, $endingDate, $displayConditions, $liabilityConcerns, $lendingReason);

    // Execute the query for the Lending table
    if ($stmt->execute()) {
        echo "Lending record added successfully!<br>";
    } else {
        echo "Error: " . $stmt->error;
        exit();
    }

    // Insert query for the Artifact table
    $sql_artifact = "INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, 
                     `additional_info`, `narrative`, `artifact_img`, `documentation`, `related_img`) 
                     VALUES (?, ?, ?, ?, ?,?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_artifact);

    $type = "Lending";  // Setting a default value for artifact type

    // Bind the parameters to the prepared statement
    $stmt->bind_param("sissssssss", $type, $donatorID, $artifactDescription, $artifactTitle, $acquisition, 
                      $additionalInfo, $narrative, $art_img_upload_path, $doc_img_upload_path, $rel_img_upload_path);

    // Execute the query
    if ($stmt->execute()) {
        echo "Artifact added successfully!<br>";
    } else {
        echo "Error: " . $stmt->error;
        exit();
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
 