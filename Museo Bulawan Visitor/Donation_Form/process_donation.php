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
    
    // Image upload handling
    $allowed_exs = array("jpg", "jpeg", "png");

    // Handle artifact image upload
    $art_img_upload_path = '';
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_name = $_FILES['artifact_img']['name'];
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                die("Error: Artifact image is too large.");
            } else {
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    $new_art_img_name = uniqid("IMG-", true) . '.' . $art_img_ex_lc;

                    // Adjust the upload path to the correct directory
                    $upload_dir = __DIR__ . '/../../admin_mis/src/uploads/artifacts/';
                    
                    // Debugging: Check if the directory exists
                    if (!is_dir($upload_dir)) {
                        die("Upload directory does not exist: " . $upload_dir);
                    }

                    // Debugging: Check if the directory is writable
                    if (!is_writable($upload_dir)) {
                        die("Upload directory is not writable.");
                    }

                    // Debugging: Check if temporary file exists
                    if (!file_exists($art_tmp_name)) {
                        die("Temporary file does not exist.");
                    }

                    $art_img_upload_path = $upload_dir . $new_art_img_name;

                    // Move the uploaded file and check for errors
                    if (!move_uploaded_file($art_tmp_name, $art_img_upload_path)) {
                        error_log("Failed to upload file: " . error_get_last()['message']);
                        die("Failed to upload file to: " . $art_img_upload_path);
                    }
                } else {
                    die("Error: Invalid artifact image type.");
                }
            }
        } else {
            die("Error: Artifact image upload error.");
        }
    }

    // Insert query for the Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_donatorTB);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    // Execute the insert query for Donator
    if ($stmt->execute()) {
        $donatorID = $conn->insert_id;  // Get the inserted ID directly
    } else {
        die("Error inserting donator: " . $stmt->error);
    }

    // Insert query for the Donation table
    $abt_art = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($abt_art);
    $stmt->bind_param("iss", $donatorID, $artifactTitle, $artifactDescription);

    // Execute the query for the Donation table
    if (!$stmt->execute()) {
        die("Error inserting donation: " . $stmt->error);
    }

    // Insert query for the Artifact table
    $artifactType = "Donation";
    $query = $conn->prepare("INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('sissssss', $artifactType, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $art_img_upload_path);

    // Execute the query for the Artifact table
    if (!$query->execute()) {
        die("Error inserting artifact: " . $query->error);
    }

    echo json_encode(['success' => true]);
    header("Location: donateindex.html?success=true");
}

// Close the connection
$conn->close();
?>
