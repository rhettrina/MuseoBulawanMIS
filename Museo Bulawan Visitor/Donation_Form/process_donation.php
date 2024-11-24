<?php
// Enable all error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Define constants for file upload
define('UPLOAD_DIR', '/uploads/artifacts/');
$allowed_exts = ['jpg', 'jpeg', 'png'];
$max_file_size = 12 * 1024 * 1024; // 12 MB

// Ensure the upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    die("The upload directory does not exist. Please create the directory: " . UPLOAD_DIR);
}
if (!is_writable(UPLOAD_DIR)) {
    die("The upload directory is not writable: " . UPLOAD_DIR);
}

// Function to handle image upload
function uploadImage($file) {
    global $allowed_exts, $max_file_size;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "Error uploading the file.";
    }

    $file_name = basename($file['name']);
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $file_mime = mime_content_type($file_tmp);

    // Validate file size
    if ($file_size > $max_file_size) {
        return "File size exceeds the maximum allowed size of 12 MB.";
    }

    // Validate file type
    $allowed_mime_types = ['image/jpeg', 'image/png'];
    if (!in_array($file_ext, $allowed_exts) || !in_array($file_mime, $allowed_mime_types)) {
        return "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
    }

    // Generate unique file name
    $new_file_name = uniqid('IMG-', true) . '.' . $file_ext;
    $upload_path = UPLOAD_DIR . $new_file_name;

    // Move uploaded file
    if (!move_uploaded_file($file_tmp, $upload_path)) {
        return "Failed to move uploaded file.";
    }

    return $upload_path;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

    // Handle artifact image upload
    $artifactImagePath = '';
    if (!empty($_FILES['artifact_img']['name'])) {
        $artifactImagePath = uploadImage($_FILES['artifact_img']);
        if (strpos($artifactImagePath, 'uploads/') === false) {
            // Error occurred during file upload
            header("Location: donateindex.html?error=" . urlencode($artifactImagePath));
            exit();
        }
    }

    // Insert data into Donator table
    $stmt = $conn->prepare("INSERT INTO Donator (first_name, last_name, email, phone, province, street, barangay, organization, age, sex, city, submission_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if (!$stmt->execute()) {
        die("Error inserting into Donator table: " . $stmt->error);
    }

    // Retrieve Donator ID
    $donatorID = $conn->insert_id;

    // Insert data into Artifact table
    $stmt = $conn->prepare("INSERT INTO Artifact (donatorID, artifact_description, artifact_nameID, acquisition, additional_info, narrative, artifact_img) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $artifactImagePath);

    if (!$stmt->execute()) {
        die("Error inserting into Artifact table: " . $stmt->error);
    }

    echo "Data saved successfully!";
    header("Location: donateindex.html?success=1");
    exit();
}

// Close the connection
$conn->close();
?>
