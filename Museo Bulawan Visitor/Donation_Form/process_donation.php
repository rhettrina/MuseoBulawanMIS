<?php
\header("Access-Control-Allow-Origin: *"); // Allow all domains, or set a specific domain
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Allow POST and GET methods


$servername = "localhost"; 
$username = "u376871621_bomb_squad";       
$password = "Fujiwara000!";            
$dbname = "u376871621_mb_mis";  

// Create connection
$connextion = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($connextion->connect_error) {
    die("Connection failed: " . $connextion->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $firstName = $connextion->real_escape_string($_POST['firstName']);
    $lastName = $connextion->real_escape_string($_POST['lastName']);
    $age = intval($_POST['age']);
    $sex = $connextion->real_escape_string($_POST['sex']);
    $email = $connextion->real_escape_string($_POST['email']);
    $phone = $connextion->real_escape_string($_POST['phone']);
    $organization = $connextion->real_escape_string($_POST['organization']);
    $province = $connextion->real_escape_string($_POST['province']);
    $city = $connextion->real_escape_string($_POST['city']);
    $barangay = $connextion->real_escape_string($_POST['barangay']);
    $street = $connextion->real_escape_string($_POST['street']);
    $artifactTitle = $connextion->real_escape_string($_POST['artifactTitle']);
    $artifactDescription = $connextion->real_escape_string($_POST['artifactDescription']);
    $acquisition = $connextion->real_escape_string($_POST['acquisition']);
    $additionalInfo = $connextion->real_escape_string($_POST['additionalInfo']);
    $narrative = $connextion->real_escape_string($_POST['narrative']);
    $linkartimg = $linkdocimg = $linkrelimg = '';

    // Image file handling
    function handleFileUpload($file, $uploadDir) {
        $allowed_exs = array("jpg", "jpeg", "png");
        $fileName = $file['name'];
        $fileSize = $file['size'];
        $tmpName = $file['tmp_name'];
        $error = $file['error'];

        if ($error === 0) {
            if ($fileSize > 12500000) {
                return ['success' => false, 'message' => 'File is too large.'];
            }
            $fileEx = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($fileEx, $allowed_exs)) {
                return ['success' => false, 'message' => "Invalid file type."];
            }
            $fileNameSanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $fileName);
            $filePath = $uploadDir . $fileNameSanitized;
            move_uploaded_file($tmpName, $filePath);
            return ['success' => true, 'fileName' => $fileNameSanitized];
        }
        return ['success' => false, 'message' => 'Error uploading the file.'];
    }

    // Handle uploads for images
    $art_img_result = !empty($_FILES['artifact_img']['name']) ? handleFileUpload($_FILES['artifact_img'], 'uploads/artifacts/') : ['success' => true];
    if (!$art_img_result['success']) {
        echo json_encode($art_img_result);
        exit();
    } else {
        $linkartimg = $art_img_result['fileName'];
    }

    $doc_img_result = !empty($_FILES['documentation_img']['name']) ? handleFileUpload($_FILES['documentation_img'], 'uploads/documentation/') : ['success' => true];
    if (!$doc_img_result['success']) {
        echo json_encode($doc_img_result);
        exit();
    } else {
        $linkdocimg = $doc_img_result['fileName'];
    }

    $rel_img_result = !empty($_FILES['related_img']['name']) ? handleFileUpload($_FILES['related_img'], 'uploads/related/') : ['success' => true];
    if (!$rel_img_result['success']) {
        echo json_encode($rel_img_result);
        exit();
    } else {
        $linkrelimg = $rel_img_result['fileName'];
    }

    // Insert data into the donation_form table
    $sql = "INSERT INTO donation_form (first_name, last_name, age, sex, email, phone, organization, province, city, barangay, street, artifact_title, artifact_description, acquisition_details, additional_info, narrative, link_art_img, artifact_images, link_doc_img, documentation, link_rel_img, related_images)
            VALUES ('$firstName', '$lastName', '$age', '$sex', '$email', '$phone', '$organization', '$province', '$city', '$barangay', '$street', '$artifactTitle', '$artifactDescription', '$acquisition', '$additionalInfo', '$narrative', '$linkartimg', '$art_img_result[fileName]', '$linkdocimg', '$doc_img_result[fileName]', '$linkrelimg', '$rel_img_result[fileName]')";

    if ($connextion->query($sql) === TRUE) {
        date_default_timezone_set('Asia/Manila'); // Set timezone

        // Prepare and insert into donations table
        $donor_name = $firstName . ' ' . $lastName;  // Concatenate names
        $item_name = $artifactTitle;  // Use artifact title
        $donation_date = (new DateTime())->format('Y-m-d');
        $status = "TO REVIEW";
        $transfer_status = "Pending"; // Default transfer status

        $sql_donations = "INSERT INTO donations (donation_date, donor_name, item_name, status, transfer_status)
                          VALUES ('$donation_date', '$donor_name', '$item_name', '$status', '$transfer_status')";

        if ($connextion->query($sql_donations) === TRUE) {
            $response = array('success' => true, 'message' => "Form submitted successfully.");
            echo json_encode($response);
        } else {
            $response = array('success' => false, 'message' => "Error inserting data into donations table.");
            echo json_encode($response);
        }
    } else {
        $response = array('success' => false, 'message' => "Error inserting data into donation_form table.");
        echo json_encode($response);
    }
}

// Close the database connection
$connextion->close();
?>
