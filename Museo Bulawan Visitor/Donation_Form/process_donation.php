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

// Function to handle single or multiple file uploads
function uploadFiles($files, $uploadDir, $allowedExtensions) {
    $uploadedPaths = [];
    // Ensure the input is handled as an array
    if (!is_array($files['name'])) {
        $files = [
            'name' => [$files['name']],
            'type' => [$files['type']],
            'tmp_name' => [$files['tmp_name']],
            'error' => [$files['error']],
            'size' => [$files['size']]
        ];
    }

    foreach ($files['name'] as $index => $name) {
        if ($files['error'][$index] === 0) {
            $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowedExtensions)) {
                $newFileName = uniqid("FILE-", true) . '.' . $fileExt;
                $fileUploadPath = $uploadDir . $newFileName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
                }

                if (move_uploaded_file($files['tmp_name'][$index], $fileUploadPath)) {
                    $uploadedPaths[] = $fileUploadPath;
                }
            }
        }
    }
    return $uploadedPaths;
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
    
    // Define upload directories (single directory for all types of files)
    $uploadDir = __DIR__ . '/../../admin_mis/src/uploads/artifacts/';
    $allowedExtensions = ["jpg", "jpeg", "png", "pdf", "docx", "xlsx", "txt"];

    // Handle file uploads
    $artifactImages = uploadFiles($_FILES['artifact_img'], $uploadDir, $allowedExtensions);
    $documentationFiles = uploadFiles($_FILES['documentation'], $uploadDir, $allowedExtensions);
    $relatedImages = uploadFiles($_FILES['related_img'], $uploadDir, $allowedExtensions);

    // Convert file paths to JSON strings for database storage
    $artifactImagesJson = json_encode($artifactImages);
    $documentationFilesJson = json_encode($documentationFiles);
    $relatedImagesJson = json_encode($relatedImages);

    // Insert data into Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_donatorTB);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if ($stmt->execute()) {
        $donatorID = $conn->insert_id; // Get the inserted ID directly
    } else {
        die(json_encode(['success' => false, 'message' => 'Error inserting donator: ' . $stmt->error]));
    }

    // Insert data into Donation table
    $abt_art = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($abt_art);
    $stmt->bind_param("iss", $donatorID, $artifactTitle, $artifactDescription);

    if (!$stmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error inserting donation: ' . $stmt->error]));
    }

    // Insert data into Artifact table
    $artifactType = "Donation";
    $query = $conn->prepare("INSERT INTO `Artifact`(`artifact_typeID`, `submission_date`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`, `documentation`, `related_img`, `status`, `transfer_status`, `updated_date`, `display_status`) 
                             VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'To Review', 'Pending', NULL, 'true')");
    $query->bind_param('sissssssss', $artifactType, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $artifactImagesJson, $documentationFilesJson, $relatedImagesJson);

    if (!$query->execute()) {
        die(json_encode(['success' => false, 'message' => 'Error inserting artifact: ' . $query->error]));
    }

    // Success response
    echo json_encode(['success' => true, 'message' => 'Data submitted successfully.']);
}

// Close the connection
$conn->close();
?>
