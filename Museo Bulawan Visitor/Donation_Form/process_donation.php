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
    
    // File upload configuration
    $allowed_exs = array("jpg", "jpeg", "png", "docx");
    $upload_dir = __DIR__ . '/../../admin_mis/src/uploads/artifacts/';
    
    // Check if the upload directory exists and is writable
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    if (!is_writable($upload_dir)) {
        die("Upload directory is not writable.");
    }

    $uploaded_files = [];

    // Function to handle file uploads
    function handleFileUpload($file_key, $upload_dir, $allowed_exs) {
        if (!empty($_FILES[$file_key]['name'])) {
            $file_name = $_FILES[$file_key]['name'];
            $file_size = $_FILES[$file_key]['size'];
            $tmp_name = $_FILES[$file_key]['tmp_name'];
            $file_error = $_FILES[$file_key]['error'];

            if ($file_error === 0) {
                if ($file_size > 12500000) {
                    return "Error: File {$file_key} is too large.";
                } else {
                    $file_ext_lc = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    if (in_array($file_ext_lc, $allowed_exs)) {
                        $new_file_name = uniqid("FILE-", true) . '.' . $file_ext_lc;
                        $file_upload_path = $upload_dir . $new_file_name;

                        if (move_uploaded_file($tmp_name, $file_upload_path)) {
                            return $file_upload_path; // Return the uploaded file path
                        } else {
                            return "Failed to upload file {$file_key}.";
                        }
                    } else {
                        return "Error: Invalid file type for {$file_key}.";
                    }
                }
            } else {
                switch ($file_error) {
                    case UPLOAD_ERR_INI_SIZE:
                        return "Error: File {$file_key} exceeds upload_max_filesize in php.ini.";
                    case UPLOAD_ERR_FORM_SIZE:
                        return "Error: File {$file_key} exceeds MAX_FILE_SIZE in HTML form.";
                    case UPLOAD_ERR_PARTIAL:
                        return "Error: File {$file_key} was only partially uploaded.";
                    case UPLOAD_ERR_NO_FILE:
                        return "Error: No file was uploaded for {$file_key}.";
                    case UPLOAD_ERR_NO_TMP_DIR:
                        return "Error: Missing a temporary folder for {$file_key}.";
                    case UPLOAD_ERR_CANT_WRITE:
                        return "Error: Failed to write file {$file_key} to disk.";
                    default:
                        return "Unknown error during file upload for {$file_key}.";
                }
            }
        }
        return null; // Return null if no file was uploaded
    }

    // List of files to handle
    $file_keys = ['artifact_img', 'documentation_img', 'related_img'];
    foreach ($file_keys as $file_key) {
        $upload_result = handleFileUpload($file_key, $upload_dir, $allowed_exs);
        if (is_string($upload_result)) {
            // Error during upload, return JSON response and exit
            echo json_encode(['success' => false, 'message' => $upload_result]);
            exit;
        }
        $uploaded_files[$file_key] = $upload_result;
    }

    // Insert query for the Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_donatorTB);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => "Prepare failed for Donator: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if ($stmt->execute()) {
        $donatorID = $conn->insert_id;
    } else {
        echo json_encode(['success' => false, 'message' => "Error inserting donator: " . $stmt->error]);
        exit;
    }

    // Insert query for the Donation table
    $abt_art = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`, `submission_date`) 
                VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($abt_art);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => "Prepare failed for Donation table: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("iss", $donatorID, $artifactTitle, $artifactDescription);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => "Error inserting donation: " . $stmt->error]);
        exit;
    }

    // Insert query for the Artifact table
    $artifactType = "Donation";
    $artifact_img_path = $uploaded_files['artifact_img'] ?? null;
    $documentation_img_path = $uploaded_files['documentation_img'] ?? null;
    $related_img_path = $uploaded_files['related_img'] ?? null;

    $query = $conn->prepare("INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`, `documentation_img`, `related_img`, `submission_date`, `updated_date`) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    if (!$query) {
        echo json_encode(['success' => false, 'message' => "Prepare failed for Artifact table: " . $conn->error]);
        exit;
    }
    $query->bind_param('sissssssss', $artifactType, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $artifact_img_path, $documentation_img_path, $related_img_path);

    if (!$query->execute()) {
        echo json_encode(['success' => false, 'message' => "Error inserting artifact: " . $query->error]);
        exit;
    }

    echo json_encode(['success' => true]);
    header("Location: donateindex.html?success=true");
}

// Close the connection
$conn->close();
?>
