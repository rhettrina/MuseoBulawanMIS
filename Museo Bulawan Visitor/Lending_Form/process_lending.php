<?php
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

    $loanDuration = $conn->real_escape_string($_POST['loanDuration']);
    $displayConditions = $conn->real_escape_string($_POST['displayConditions']);
    $liabilityConcerns = $conn->real_escape_string($_POST['liabilityConcerns']);
    $displayConditions = $conn->real_escape_string($_POST['lendingReason']);


    $artifactTitle = $conn->real_escape_string($_POST['artifactTitle']);
    $artifactDescription = $conn->real_escape_string($_POST['artifactDescription']);
    $acquisition = $conn->real_escape_string($_POST['acquisition']);
    $additionalInfo = $conn->real_escape_string($_POST['additionalInfo']);
    $narrative = $conn->real_escape_string($_POST['narrative']);

    
    $linkartimg = $conn->real_escape_string($_POST['artifactImages']);
    $linkdocimg = $conn->real_escape_string($_POST['documentation']);
    $linkrelimg = $conn->real_escape_string($_POST['relatedImages']);

    // Initialize image file paths
    $art_img_upload_path = '';
    $doc_img_upload_path = '';
    $rel_img_upload_path = '';

    $allowed_exs = array("jpg", "jpeg", "png");

    // Collect status options
    $status = $conn->real_escape_string($_POST['status']); // e.g., 'to review', 'accepted', 'rejected'
    $transfer_status = $conn->real_escape_string($_POST['transfer_status']); // e.g., 'acquired', 'failed', 'pending'

    // Handle image uploads if they are set
    $art_img_name = $_FILES['artifact_img']['name'];
    if (!empty($art_img_name)) {
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                $em = "Sorry, the artifact image is too large.";
                header("Location: lendindex.php?error=$em");
                exit();
            } else {
                // Get file extension and convert to lowercase
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    // Generate unique name and path, and move file
                    $new_art_img_name = uniqid("IMG-", true) . '.' . $art_img_ex_lc;
                    $art_img_upload_path = 'uploads/artifacts/' . $new_art_img_name;
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);
                } else {
                    $em = "You can't upload files of this type for artifact image.";
                    header("Location: lendindex.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the artifact image.";
            header("Location: lendindex.php?error=$em");
            exit();
        }
    }

    $doc_img_name = $_FILES['documentation_img']['name'];
    if (!empty($doc_img_name)) {
        $doc_img_size = $_FILES['documentation_img']['size'];
        $doc_tmp_name = $_FILES['documentation_img']['tmp_name'];
        $doc_error = $_FILES['documentation_img']['error'];

        if ($doc_error === 0) {
            if ($doc_img_size > 12500000) {
                $em = "Sorry, the documentation image is too large.";
                header("Location: lendindex.php?error=$em");
                exit();
            } else {
                // Get file extension and convert to lowercase
                $doc_img_ex_lc = strtolower(pathinfo($doc_img_name, PATHINFO_EXTENSION));
                if (in_array($doc_img_ex_lc, $allowed_exs)) {
                    // Generate unique name and path, and move file
                    $new_doc_img_name = uniqid("IMG-", true) . '.' . $doc_img_ex_lc;
                    $doc_img_upload_path = 'uploads/documentation/' . $new_doc_img_name;
                    move_uploaded_file($doc_tmp_name, $doc_img_upload_path);
                } else {
                    $em = "You can't upload files of this type for documentation image.";
                    header("Location: lendindex.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the documentation image.";
            header("Location: lendindex.php?error=$em");
            exit();
        }
    }

    $rel_img_name = $_FILES['related_img']['name'];
    if (!empty($rel_img_name)) {
        $rel_img_size = $_FILES['related_img']['size'];
        $rel_tmp_name = $_FILES['related_img']['tmp_name'];
        $rel_error = $_FILES['related_img']['error'];

        if ($rel_error === 0) {
            if ($rel_img_size > 12500000) {
                $em = "Sorry, the related image is too large.";
                header("Location: lendindex.php?error=$em");
                exit();
            } else {
                // Get file extension and convert to lowercase
                $rel_img_ex_lc = strtolower(pathinfo($rel_img_name, PATHINFO_EXTENSION));
                if (in_array($rel_img_ex_lc, $allowed_exs)) {
                    // Generate unique name and path, and move file
                    $new_rel_img_name = uniqid("IMG-", true) . '.' . $rel_img_ex_lc;
                    $rel_img_upload_path = 'uploads/related/' . $new_rel_img_name;
                    move_uploaded_file($rel_tmp_name, $rel_img_upload_path);
                } else {
                    $em = "You can't upload files of this type for related image.";
                    header("Location: lendindex.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the related image.";
            header("Location: lendindex.php?error=$em");
            exit();
        }
    }

    // Insert all data into the donation_form table
    $sql = "INSERT INTO lending_form (first_name, last_name, age, sex, email, phone, organization, province, city, barangay, street, loan_duration, display_conditions, liability_concerns, lending_reason, artifact_title, artifact_description, acquisition_details, additional_info, narrative, link_art_img, artifact_images, link_doc_img, documentation, link_rel_img, related_images)
            VALUES ('$firstName', '$lastName', '$age', '$sex', '$email', '$phone', '$organization', '$province', '$city', '$barangay', '$street', '$loanDuration', '$displayConditions', '$liabilityConcerns', '$lendingReason', '$artifactTitle', '$artifactDescription', '$acquisition', '$additionalInfo', '$narrative', '$linkartimg', '$art_img_name', '$linkdocimg', '$doc_img_name', '$linkrelimg', '$rel_img_name')";

    if ($conn->query($sql) === TRUE) {

        
        date_default_timezone_set('Asia/Manila'); // or use your desired timezone

        // Prepare and insert into donations table
        $donor_name = $firstName . ' ' . $lastName;  // Concatenate first and last name
        $item_name = $artifactTitle;  // Use artifact title directly
        $donation_date = (new DateTime())->format('Y-m-d'); // Get the current date explicitly
        $status = "TO REVIEW";
        $transfer_status = "PENDING";

        // Insert into donations table
        $donation_sql = "INSERT INTO donations (donor_name, item_name, donation_date, status, transfer_status)
                         VALUES ('$donor_name', '$item_name', '$donation_date', '$status', '$transfer_status')";

        if ($conn->query($donation_sql) === TRUE) {
            echo "Donation submitted successfully!";
            header("Location: lendindex.php");
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
