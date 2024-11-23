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

    // Image upload paths
    $allowed_exs = array("jpg", "jpeg", "png");
    $art_img_upload_path = $doc_img_upload_path = $rel_img_upload_path = '';

    // Artifact image upload
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_name = handle_file_upload('artifact_img', $allowed_exs, 'uploads/artifacts/');
        $art_img_upload_path = $art_img_name;
    }

    // Documentation image upload
    if (!empty($_FILES['doc_img']['name'])) {
        $doc_img_name = handle_file_upload('doc_img', $allowed_exs, 'uploads/documentation/');
        $doc_img_upload_path = $doc_img_name;
    }

    // Related image upload
    if (!empty($_FILES['rel_img']['name'])) {
        $rel_img_name = handle_file_upload('rel_img', $allowed_exs, 'uploads/related/');
        $rel_img_upload_path = $rel_img_name;
    }

    // Insert into Donator table
    $sql_donator = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`, `submission_date`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_donator);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);
    if (!$stmt->execute()) {
        die("Error inserting Donator: " . $stmt->error);
    }

    // Retrieve Donator ID
    $donatorID = $conn->insert_id;

    // Insert into Donation table
    $sql_donation = "INSERT INTO `Donation`(`donatorID`, `artifact_nameID`, `artifact_description`, `submission_date`) 
                     VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_donation);
    $stmt->bind_param("iss", $donatorID, $artifactTitle, $artifactDescription);
    if (!$stmt->execute()) {
        die("Error inserting Donation: " . $stmt->error);
    }

    // Insert into Artifact table
    $sql_artifact = "INSERT INTO `Artifact`(`artifact_typeID`, `donatorID`, `artifact_description`, `artifact_nameID`, `acquisition`, `additional_info`, `narrative`, `artifact_img`, `documentation`, `related_img`, `submission_date`) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $type = "Donation";
    $stmt = $conn->prepare($sql_artifact);
    $stmt->bind_param("sissssssss", $type, $donatorID, $artifactDescription, $artifactTitle, $acquisition, $additionalInfo, $narrative, $art_img_upload_path, $doc_img_upload_path, $rel_img_upload_path);
    if (!$stmt->execute()) {
        die("Error inserting Artifact: " . $stmt->error);
    }

    // Success
    echo "All data inserted successfully!";
    header("Location: donateindex.html?success=true");
    exit();
}

// Close the connection
$conn->close();

// Function to handle file uploads
function handle_file_upload($fileKey, $allowed_exs, $uploadDir) {
    $file = $_FILES[$fileKey];
    $file_name = $file['name'];
    $file_size = $file['size'];
    $tmp_name = $file['tmp_name'];
    $error = $file['error'];

    if ($error === 0) {
        if ($file_size > 12500000) {
            die("Error: File size too large for $fileKey.");
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (in_array($file_ext, $allowed_exs)) {
            $new_file_name = uniqid("IMG-", true) . '.' . $file_ext;
            $file_upload_path = $uploadDir . $new_file_name;
            if (!move_uploaded_file($tmp_name, $file_upload_path)) {
                die("Error moving uploaded file for $fileKey.");
            }
            return $file_upload_path;
        } else {
            die("Error: Invalid file type for $fileKey.");
        }
    } else {
        die("Error uploading file for $fileKey.");
    }
}
