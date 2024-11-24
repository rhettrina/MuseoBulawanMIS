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

    // Allowed image extensions
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");

    // Handle Artifact Image Upload
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_name = $_FILES['artifact_img']['name'];
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 3 * 1024 * 1024) {
                $em = "Artifact image size exceeds 3MB.";
                header("Location: donateindex.html?error=$em");
                exit();
            } else {
                $art_img_extension = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_extension, $allowed_extensions)) {
                    $new_art_img_name = uniqid("IMG-ART-", true) . '.' . $art_img_extension;
                    $art_img_upload_path = 'uploads/artifacts/' . $new_art_img_name;
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);
                } else {
                    $em = "Invalid file type for Artifact image.";
                    header("Location: donateindex.html?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading Artifact image.";
            header("Location: donateindex.html?error=$em");
            exit();
        }
    }

    // Handle Documentation Image Upload
    if (!empty($_FILES['documentation']['name'])) {
        $doc_img_name = $_FILES['documentation']['name'];
        $doc_img_size = $_FILES['documentation']['size'];
        $doc_tmp_name = $_FILES['documentation']['tmp_name'];
        $doc_error = $_FILES['documentation']['error'];

        if ($doc_error === 0) {
            if ($doc_img_size > 3 * 1024 * 1024) {
                $em = "Documentation image size exceeds 3MB.";
                header("Location: donateindex.html?error=$em");
                exit();
            } else {
                $doc_img_extension = strtolower(pathinfo($doc_img_name, PATHINFO_EXTENSION));
                if (in_array($doc_img_extension, $allowed_extensions)) {
                    $new_doc_img_name = uniqid("IMG-DOC-", true) . '.' . $doc_img_extension;
                    $doc_img_upload_path = 'uploads/documentation/' . $new_doc_img_name;
                    move_uploaded_file($doc_tmp_name, $doc_img_upload_path);
                } else {
                    $em = "Invalid file type for Documentation image.";
                    header("Location: donateindex.html?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading Documentation image.";
            header("Location: donateindex.html?error=$em");
            exit();
        }
    }

    // Handle Related Image Upload
    if (!empty($_FILES['related_img']['name'])) {
        $rel_img_name = $_FILES['related_img']['name'];
        $rel_img_size = $_FILES['related_img']['size'];
        $rel_tmp_name = $_FILES['related_img']['tmp_name'];
        $rel_error = $_FILES['related_img']['error'];

        if ($rel_error === 0) {
            if ($rel_img_size > 3 * 1024 * 1024) {
                $em = "Related image size exceeds 3MB.";
                header("Location: donateindex.html?error=$em");
                exit();
            } else {
                $rel_img_extension = strtolower(pathinfo($rel_img_name, PATHINFO_EXTENSION));
                if (in_array($rel_img_extension, $allowed_extensions)) {
                    $new_rel_img_name = uniqid("IMG-REL-", true) . '.' . $rel_img_extension;
                    $rel_img_upload_path = 'uploads/related/' . $new_rel_img_name;
                    move_uploaded_file($rel_tmp_name, $rel_img_upload_path);
                } else {
                    $em = "Invalid file type for Related image.";
                    header("Location: donateindex.html?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading Related image.";
            header("Location: donateindex.html?error=$em");
            exit();
        }
    }

    // Insert data into Donator table
    $sql_donatorTB = "INSERT INTO `Donator`(`first_name`, `last_name`, `email`, `phone`, `province`, `street`, `barangay`, `organization`, `age`, `sex`, `city`, `submission_date`) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql_donatorTB);
    $stmt->bind_param("ssssssssiss", $firstName, $lastName, $email, $phone, $province, $street, $barangay, $organization, $age, $sex, $city);

    if ($stmt->execute()) {
        echo "Donator added successfully!<br>";
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
