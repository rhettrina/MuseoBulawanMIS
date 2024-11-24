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
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
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
    
    // Initialize variables for artifact image upload
    $art_img_upload_path = '';
    $allowed_exs = ["jpg", "jpeg", "png", "docx"];

    // Handle artifact image upload if provided
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_name = $_FILES['artifact_img']['name'];
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                die(json_encode(['success' => false, 'message' => 'Artifact image is too large.']));
            }

            $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
            if (in_array($art_img_ex_lc, $allowed_exs)) {
                $new_art_img_name = uniqid("IMG-", true) . '.' . $art_img_ex_lc;
                $upload_dir = __DIR__ . '/../../admin_mis/src/uploads/artifacts/';

                if (!is_dir($upload_dir)) {
                    die(json_encode(['success' => false, 'message' => 'Upload directory does not exist.']));
                }

                if (!is_writable($upload_dir)) {
                    die(json_encode(['success' => false, 'message' => 'Upload directory is not writable.']));
                }

                $art_img_upload_path = $upload_dir . $new_art_img_name;

                if (!move_uploaded_file($art_tmp_name, $art_img_upload_path)) {
                    die(json_encode(['success' => false, 'message' => 'Failed to upload artifact image.']));
                }
            } else {
                die(json_encode(['success' => false, 'message' => 'Invalid artifact image type.']));
            }
        } else {
            die(json_encode(['success' => false, 'message' => 'Artifact image upload error.']));
        }
    }

    // Insert query for the Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_donatorTB);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if ($stmt->execute()) {
        $donatorID = $conn->insert_id; // Get the inserted ID directly
    } else {
        die(json_encode(['success' => false, 'message' => 'Error inserting donator: ' . $stmt->error]));
    }

    // Insert query for the Donation table
    $abt_art = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($abt_art);
    $stmt->bind_param("iss", $donatorID, $artifactTitle, $artifactDescription);

    if (!$stmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error inserting donation: ' . $stmt->error]));
    }

    // Insert query for the Artifact table
    $artifactType = "Donation";
    $query = $conn->prepare("INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param('sissssss', $artifactType, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $art_img_upload_path);

    if (!$query->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error inserting artifact: ' . $query->error]));
    }

    // Success response
    echo json_encode(['success' => true, 'message' => 'Data submitted successfully.']);
    exit;
}

// Close the connection
$conn->close();
?>
