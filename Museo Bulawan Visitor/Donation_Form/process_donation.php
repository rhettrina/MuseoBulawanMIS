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
                    die("Error: File {$file_key} is too large.");
                } else {
                    $file_ext_lc = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    if (in_array($file_ext_lc, $allowed_exs)) {
                        $new_file_name = uniqid("FILE-", true) . '.' . $file_ext_lc;
                        $file_upload_path = $upload_dir . $new_file_name;

                        if (move_uploaded_file($tmp_name, $file_upload_path)) {
                            return $file_upload_path; // Return the uploaded file path
                        } else {
                            die("Failed to upload file {$file_key}.");
                        }
                    } else {
                        die("Error: Invalid file type for {$file_key}.");
                    }
                }
            } else {
                die("Error: File upload error for {$file_key}.");
            }
        }
        return null; // Return null if no file was uploaded
    }

    // List of files to handle
    $file_keys = ['artifact_img', 'documentation_img', 'related_img'];
    foreach ($file_keys as $file_key) {
        $uploaded_files[$file_key] = handleFileUpload($file_key, $upload_dir, $allowed_exs);
    }

    // Insert query for the Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_donatorTB);
    if (!$stmt) {
        die("Prepare failed for Donator: " . $conn->error);
    }
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if ($stmt->execute()) {
        $donatorID = $conn->insert_id;  // Get the inserted ID directly
    } else {
        die("Error inserting donator: " . $stmt->error);
    }

    // Insert query for the Donation table
    $abt_art = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`, `submission_date`) 
                VALUES (?, ?, ?, NOW())";
    echo "Preparing the query for Donation table...\n";
    $stmt = $conn->prepare($abt_art);
    if (!$stmt) {
        die("Prepare failed for Donation table: " . $conn->error);
    }
    $stmt->bind_param("iss", $donatorID, $artifactTitle, $artifactDescription);
    if (!$stmt->execute()) {
        die("Error inserting donation: " . $stmt->error);
    }

    // Insert query for the Artifact table
    $artifactType = "Donation";
    $artifact_img_path = $uploaded_files['artifact_img'] ?? null;
    $documentation_img_path = $uploaded_files['documentation_img'] ?? null;
    $related_img_path = $uploaded_files['related_img'] ?? null;

    $query = $conn->prepare("INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`, `documentation_img`, `related_img`, `submission_date`, `updated_date`) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    if (!$query) {
        die("Prepare failed for Artifact table: " . $conn->error);
    }
    $query->bind_param('sissssssss', $artifactType, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $artifact_img_path, $documentation_img_path, $related_img_path);

    if (!$query->execute()) {
        die("Error inserting artifact: " . $query->error);
    }

    echo json_encode(['success' => true]);
    header("Location: donateindex.html?success=true");
}

// Close the connection
$conn->close();
?>
