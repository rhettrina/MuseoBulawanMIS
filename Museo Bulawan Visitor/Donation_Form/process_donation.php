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
define('UPLOAD_DIR', __DIR__ . '/uploads/artifacts/'); // Use the script's directory as the base

// Ensure the upload directory exists
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true); // Create the directory if it doesn't exist
}

// Function to handle image upload
function uploadImage($image) {
    $targetDir = UPLOAD_DIR;
    $targetFile = $targetDir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $errorMessage = "";

    // Check if the file is an image
    if (getimagesize($image["tmp_name"]) === false) {
        $errorMessage = "File is not an image.";
        return $errorMessage;
    }

    // If file already exists, use the same path
    if (file_exists($targetFile)) {
        return $targetFile; // Return the existing file path
    }

    // Check file size (max 12MB)
    if ($image["size"] > 12 * 1024 * 1024) {
        $errorMessage = "File size exceeds 12MB.";
        return $errorMessage;
    }

    // Check file format (allowed types: jpg, jpeg, png, gif)
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
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
        if (strpos($artifactImagePath, UPLOAD_DIR) === false) {
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
    $donatorID = $stmt->insert_id;

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
