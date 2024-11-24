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
    
    $artifactTitle = $conn->real_escape_string($_POST['artifactTitle']);
    $artifactDescription = $conn->real_escape_string($_POST['artifactDescription']);
    $acquisition = $conn->real_escape_string($_POST['acquisition']);
    $additionalInfo = $conn->real_escape_string($_POST['additionalInfo']);
    $narrative = $conn->real_escape_string($_POST['narrative']);
    
    $formType = $conn->real_escape_string($_POST['formType']);
    
    // Initialize image file paths
    $art_img_upload_path = '';
    $doc_img_upload_path = '';
    $rel_img_upload_path = '';

    // Handle image uploads using the logic from the article code
    function uploadImage($image, $targetDir) {
        $targetFile = $targetDir . basename($image["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if the file is an image
        if (getimagesize($image["tmp_name"]) === false) {
            return false; // File is not an image
        }

        // If file already exists, use the same path
        if (file_exists($targetFile)) {
            return $targetFile; // Return the existing file path
        }

        // Check file size (max 3MB)
        if ($image["size"] > 3 * 1024 * 1024) {
            return false; // File size exceeds 3MB
        }

        // Check file format (allowed types: jpg, jpeg, png, gif)
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            return false; // Invalid file type
        }

        // Try to move the uploaded file to the target directory
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            return $targetFile; // Return the file path
        } else {
            return false; // Error uploading the file
        }
    }

    // Set upload directories
    $artifactUploadDir = 'uploads/artifacts/';
    $documentationUploadDir = 'uploads/documentation/';
    $relatedUploadDir = 'uploads/related/';

    // Upload images
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_upload_path = uploadImage($_FILES['artifact_img'], $artifactUploadDir);
        if (!$art_img_upload_path) {
            echo json_encode(['success' => false, 'error' => 'Failed to upload artifact image']);
            exit();
        }
    }

    if (!empty($_FILES['documentation']['name'])) {
        $doc_img_upload_path = uploadImage($_FILES['documentation'], $documentationUploadDir);
        if (!$doc_img_upload_path) {
            echo json_encode(['success' => false, 'error' => 'Failed to upload documentation image']);
            exit();
        }
    }

    if (!empty($_FILES['related_img']['name'])) {
        $rel_img_upload_path = uploadImage($_FILES['related_img'], $relatedUploadDir);
        if (!$rel_img_upload_path) {
            echo json_encode(['success' => false, 'error' => 'Failed to upload related image']);
            exit();
        }
    }

    // Insert query for the Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`, `submission_date`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_donatorTB);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if ($stmt->execute()) {
        echo "Donator added successfuslly!<br>";
    } else {
        echo "Error inserting Donator: " . $stmt->error;
        exit();
    }

    // Fetch Donator ID
    $donatorID = $conn->insert_id;

    // Insert data into Artifact table
    $sql_artifact = "INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`, `documentation`, `related_img`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $type = "Donation";
    $stmt = $conn->prepare($sql_artifact);
    $stmt->bind_param("sissssssss", $type, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $art_img_upload_path, $doc_img_upload_path, $rel_img_upload_path);

    if ($stmt->execute()) {
        echo "Artifact added successfully!<br>";
        header("Location: donateindex.html?success=true");
    } else {
        echo "Error inserting Artifact: " . $stmt->error;
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
