<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, UPDATE");
header("Access-Control-Allow-Headers: Content-Type, x-requested-with");
header('Content-Type: application/json');  // Set JSON header for the response

include('admin_mis\src\php\db_connect.php');

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

    $linkartimg = $connextion->real_escape_string($_POST['artifactImages']);
    $linkdocimg = $connextion->real_escape_string($_POST['documentation']);
    $linkrelimg = $connextion->real_escape_string($_POST['relatedImages']);

    // Initialize image file paths
    $art_img_upload_path = '';
    $doc_img_upload_path = '';
    $rel_img_upload_path = '';

    $allowed_exs = array("jpg", "jpeg", "png");

    // Collect status options
    $status = $connextion->real_escape_string($_POST['status']);
    $transfer_status = $connextion->real_escape_string($_POST['transfer_status']);

    // Handle artifact image upload
    $art_img_name = $_FILES['artifact_img']['name'];
    if (!empty($art_img_name)) {
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                $response = array('success' => false, 'message' => 'Sorry, the artifact image is too large.');
                echo json_encode($response);
                exit();
            } else {
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    $art_img_name_sanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $art_img_name);
                    $art_img_upload_path = 'C:/Users/TRISHA/.vscode/sadge/MuseoBulawanMIS/admin_mis/src/uploads/artifacts/' . $art_img_name_sanitized;

                    // Move the file to the folder
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);

                    // Insert the sanitized file name into the database
                    $art_img_name = $art_img_name_sanitized;
                } else {
                    $response = array('success' => false, 'message' => "You can't upload files of this type for artifact image.");
                    echo json_encode($response);
                    exit();
                }
            }
        } else {
            $response = array('success' => false, 'message' => "Error uploading the artifact image.");
            echo json_encode($response);
            exit();
        }
    }

    // Handle documentation image upload
    $doc_img_name = $_FILES['documentation_img']['name'];
    if (!empty($doc_img_name)) {
        $doc_img_size = $_FILES['documentation_img']['size'];
        $doc_tmp_name = $_FILES['documentation_img']['tmp_name'];
        $doc_error = $_FILES['documentation_img']['error'];

        if ($doc_error === 0) {
            if ($doc_img_size > 12500000) {
                $response = array('success' => false, 'message' => 'Sorry, the documentation image is too large.');
                echo json_encode($response);
                exit();
            } else {
                $doc_img_ex_lc = strtolower(pathinfo($doc_img_name, PATHINFO_EXTENSION));
                if (in_array($doc_img_ex_lc, $allowed_exs)) {
                    $doc_img_name_sanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $doc_img_name);
                    $doc_img_upload_path = 'C:/Users/TRISHA/.vscode/sadge/MuseoBulawanMIS/admin_mis/src/uploads/documentation/' . $doc_img_name_sanitized;
                    move_uploaded_file($doc_tmp_name, $doc_img_upload_path);

                    // Insert the sanitized file name into the database
                    $linkdocimg = $doc_img_name_sanitized;
                } else {
                    $response = array('success' => false, 'message' => "You can't upload files of this type for documentation image.");
                    echo json_encode($response);
                    exit();
                }
            }
        } else {
            $response = array('success' => false, 'message' => "Error uploading the documentation image.");
            echo json_encode($response);
            exit();
        }
    }

    // Handle related image upload
    $rel_img_name = $_FILES['related_img']['name'];
    if (!empty($rel_img_name)) {
        $rel_img_size = $_FILES['related_img']['size'];
        $rel_tmp_name = $_FILES['related_img']['tmp_name'];
        $rel_error = $_FILES['related_img']['error'];

        if ($rel_error === 0) {
            if ($rel_img_size > 12500000) {
                $response = array('success' => false, 'message' => 'Sorry, the related image is too large.');
                echo json_encode($response);
                exit();
            } else {
                $rel_img_ex_lc = strtolower(pathinfo($rel_img_name, PATHINFO_EXTENSION));
                if (in_array($rel_img_ex_lc, $allowed_exs)) {
                    $rel_img_name_sanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $rel_img_name);
                    $rel_img_upload_path = 'C:/Users/TRISHA/.vscode/sadge/MuseoBulawanMIS/admin_mis/src/uploads/related/' . $rel_img_name_sanitized;
                    move_uploaded_file($rel_tmp_name, $rel_img_upload_path);

                    // Insert the sanitized file name into the database
                    $linkrelimg = $rel_img_name_sanitized;
                } else {
                    $response = array('success' => false, 'message' => "You can't upload files of this type for related image.");
                    echo json_encode($response);
                    header("Location: donateindex.html");
                    exit(); 
                    
                }
            }
        } else {
            $response = array('success' => false, 'message' => "Error uploading the related image.");
            echo json_encode($response);
            exit();
        }
    }

    // Insert data into the donation_form table
    $sql = "INSERT INTO donation_form (first_name, last_name, age, sex, email, phone, organization, province, city, barangay, street, artifact_title, artifact_description, acquisition_details, additional_info, narrative, link_art_img, artifact_images, link_doc_img, documentation, link_rel_img, related_images)
            VALUES ('$firstName', '$lastName', '$age', '$sex', '$email', '$phone', '$organization', '$province', '$city', '$barangay', '$street', '$artifactTitle', '$artifactDescription', '$acquisition', '$additionalInfo', '$narrative', '$linkartimg', '$art_img_name_sanitized', '$linkdocimg', '$doc_img_name_sanitized', '$linkrelimg', '$rel_img_name_sanitized')";

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
            // Respond with a success message
            $response = array('success' => true, 'message' => 'Donation form submitted successfully!');
            echo json_encode($response);
        } else {
            $response = array('success' => false, 'message' => 'Error inserting data into donations table.');
            echo json_encode($response);
        }
    } else {
        $response = array('success' => false, 'message' => 'Error inserting data into donation_form table.');
        echo json_encode($response);
    }
}
?>
