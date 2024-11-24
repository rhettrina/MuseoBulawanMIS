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
                header("Location: donateindex.html?error=$em");
                exit();
            } else {
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    $new_art_img_name = uniqid("IMG-", true) . '.' . $art_img_ex_lc;
                    $art_img_upload_path = 'uploads/artifacts/' . $new_art_img_name;
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);
                } else {
                    $em = "You can't upload files of this type for artifact image.";
                    header("Location: donateindex.html?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the artifact image.";
            header("Location: donateindex.html?error=$em");
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
        header("Location: donateindex.html?error=$em");
        echo "Donator added successfully!<br>";
    } else {
        echo "Error inserting donator: " . $stmt->error;
        exit();
    }

    // Now retrieve the donatorID from the Donator table
    $fk_selector = "SELECT `donatorID` FROM `Donator` WHERE `first_name` = ? AND `last_name` = ? AND `email` = ? AND `phone` = ? AND `province` = ? AND `street` = ? AND `barangay` = ? AND `organization` = ? AND `age` = ? AND `sex` = ? AND `city` = ?";
    $stmt = $conn->prepare($fk_selector);
    $stmt->bind_param("ssssssssis", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

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

    // Insert query for the Donation table
    $abt_art = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($abt_art);
    $stmt->bind_param("sss", $donatorID, $artifactTitle, $artifactDescription);

    // Execute the query for the Donation table
    if ($stmt->execute()) {
        echo "Artifact donation added successfully!<br>";
        header("Location: donateindex.html?error=$em");
    } else {
        echo "Error: " . $stmt->error;
        exit();
    }

    // Insert query for the Artifact table
    $image1 = $_FILES['artifactImages'];

    $image1Path = uploadImage($image1);
    // Fetch Donator ID
    $donatorID = $conn->insert_id;

    if($image1Path){
        $query = $conn->prepare("INSERT INTO Artifact(artifact_typeID, donatorID, artifact_description, artifact_nameID, acquisition, additional_info, narrative, artifact_img, documentation, related_img) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
        $type = "Donation";
        $query->bind_param('sissssssss', $type, $acquisition, $additionalInfo, $narrative, $image1Path, $doc_img_upload_path, $rel_img_upload_path);

    if ($query->execute()) {
        echo json_encode(['success' => true]);
        header("Location: donateindex.html?success=true");
    } else {
        echo json_encode(['success' => false, 'error' => $query->error]);
    }
}else {
        echo json_encode(['success' => false, 'error' => 'Image upload failed']);
    }

    function uploadImage($image) {
        $targetDir = dirname(__DIR__, 1) . "/uploads/articlesUploads/";
        $targetFile = $targetDir . basename($image["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $errorMessage ="";
    
        if (getimagesize($image["tmp_name"]) === false) {
            $errorMessage = "File is not an image.";
            return $errorMessage;
        }
    
        // If file already exists, use the same path
        if (file_exists($targetFile)) {
            return $targetFile; // Return the existing file path
        }
    
        // Check file size (max 3MB)
        if ($image["size"] > 3 * 1024 * 1024) {
            $errorMessage = "File size exceeds 3MB.";
            return $errorMessage;
        }

        if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
            $errorMessage = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            return $errorMessage;
        }

        // Try to move the uploaded file to the target directory
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            return $targetFile; // Return the file path
        } else {
            $errorMessage = "Error uploading the file: " . $image["error"];
            return $errorMessage;
        }
    }
}

// Close the connection
$conn->close();
?>
