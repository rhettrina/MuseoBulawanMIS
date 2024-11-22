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

    // Collect additional form data specific to lending
    $loanDuration = $conn->real_escape_string($_POST['loanDuration']);
    $displayConditions = $conn->real_escape_string($_POST['displayConditions']);
    $liabilityConcerns = $conn->real_escape_string($_POST['liabilityConcerns']);
    $lendingReason = $conn->real_escape_string($_POST['lendingReason']);

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

    // Image upload handling
    $allowed_exs = array("jpg", "jpeg", "png");

    // Handle artifact image upload
    if (!empty($_FILES['artifact_img']['name'])) {
        $art_img_name = $_FILES['artifact_img']['name'];
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                $em = "Sorry, the artifact image is too large.";
                header("Location: lendindex.html?error=$em");
                exit();
            } else {
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    $new_art_img_name = uniqid("IMG-", true) . '.' . $art_img_ex_lc;
                    $art_img_upload_path = 'uploads/artifacts' . $new_art_img_name;
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);
                } else {
                    $em = "You can't upload files of this type for artifact image.";
                    header("Location: lendindex.html?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the artifact image.";
            header("Location: lendindex.html?error=$em");
            exit();
        }
    }

    // Handle other image uploads similarly (documentation, related images)
    // ...

    // Insert into lending_form table
    $sql = "INSERT INTO lending_form (first_name, last_name, age, sex, email, phone, organization, province, city, barangay, street, loan_duration, display_conditions, liability_concerns, lending_reason, artifact_title, artifact_description, acquisition_details, additional_info, narrative, link_art_img, artifact_images, link_doc_img, documentation, link_rel_img, related_images)
            VALUES ('$firstName', '$lastName', '$age', '$sex', '$email', '$phone', '$organization', '$province', '$city', '$barangay', '$street', '$loanDuration', '$displayConditions', '$liabilityConcerns', '$lendingReason', '$artifactTitle', '$artifactDescription', '$acquisition', '$additionalInfo', '$narrative', '$linkartimg', '$art_img_name', '$linkdocimg', '$doc_img_name', '$linkrelimg', '$rel_img_name')";

    if ($conn->query($sql) === TRUE) {
        date_default_timezone_set('Asia/Manila');
        $donor_name = $firstName . ' ' . $lastName;
        $item_name = $artifactTitle;
        $donation_date = (new DateTime())->format('Y-m-d');
        $status = "TO REVIEW";
        $transfer_status = "PENDING";

        // Insert into donations table
        $donation_sql = "INSERT INTO donations (donor_name, item_name, type, donation_date, status, transfer_status)
                         VALUES ('$donor_name', '$item_name', '$formType', '$donation_date', '$status', '$transfer_status')";

        if ($conn->query($donation_sql) === TRUE) {
            echo "Donation submitted successfully!";
            header("Location: lendindex.html");
        } else {
            echo "Error inserting into donations table: " . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
