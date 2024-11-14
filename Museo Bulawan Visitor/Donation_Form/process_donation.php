<?php
 $database_server = "localhost";
 $database_user = "u376871621_bomb_squad";
 $database_password = "Fujiwara000!";
 $database_name = "u376871621_mb_mis";
 $db = mysqli_connect($database_server, $database_user, $database_password, $database_name);


 if ($db->connect_error) {
     die("Connection failed: " . $db->connect_error);
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

    // Handle artifact image upload if it is set
    $art_img_name = $_FILES['artifact_img']['name'];
    if (!empty($art_img_name)) {
        $art_img_size = $_FILES['artifact_img']['size'];
        $art_tmp_name = $_FILES['artifact_img']['tmp_name'];
        $art_error = $_FILES['artifact_img']['error'];

        if ($art_error === 0) {
            if ($art_img_size > 12500000) {
                $em = "Sorry, the artifact image is too large.";
                header("Location: donateindex.php?error=$em");
                exit();
            } else {
                $art_img_ex_lc = strtolower(pathinfo($art_img_name, PATHINFO_EXTENSION));
                if (in_array($art_img_ex_lc, $allowed_exs)) {
                    // Sanitize the artifact image file name
                    $art_img_name_sanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $art_img_name);
                    $art_img_upload_path = 'formview/img/' . $art_img_name_sanitized;

                    // Move the file to the folder
                    move_uploaded_file($art_tmp_name, $art_img_upload_path);

                    // Insert the sanitized file name into the database
                    $art_img_name = $art_img_name_sanitized;
                } else {
                    $em = "You can't upload files of this type for artifact image.";
                    header("Location: donateindex.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the artifact image.";
            header("Location: donateindex.php?error=$em");
            exit();
        }
    }

    // Handle documentation image upload if it is set
    $doc_img_name = $_FILES['documentation_img']['name'];
    if (!empty($doc_img_name)) {
        $doc_img_size = $_FILES['documentation_img']['size'];
        $doc_tmp_name = $_FILES['documentation_img']['tmp_name'];
        $doc_error = $_FILES['documentation_img']['error'];

        if ($doc_error === 0) {
            if ($doc_img_size > 12500000) {
                $em = "Sorry, the documentation image is too large.";
                header("Location: donateindex.php?error=$em");
                exit();
            } else {
                $doc_img_ex_lc = strtolower(pathinfo($doc_img_name, PATHINFO_EXTENSION));
                if (in_array($doc_img_ex_lc, $allowed_exs)) {
                    $doc_img_name_sanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $doc_img_name);
                    $doc_img_upload_path = 'uploads/documentation/' . $doc_img_name_sanitized;
                    move_uploaded_file($doc_tmp_name, $doc_img_upload_path);

                    // Insert the sanitized file name into the database
                    $linkdocimg = $doc_img_name_sanitized;
                } else {
                    $em = "You can't upload files of this type for documentation image.";
                    header("Location: donateindex.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the documentation image.";
            header("Location: donateindex.php?error=$em");
            exit();
        }
    }

    // Handle related image upload if it is set
    $rel_img_name = $_FILES['related_img']['name'];
    if (!empty($rel_img_name)) {
        $rel_img_size = $_FILES['related_img']['size'];
        $rel_tmp_name = $_FILES['related_img']['tmp_name'];
        $rel_error = $_FILES['related_img']['error'];

        if ($rel_error === 0) {
            if ($rel_img_size > 12500000) {
                $em = "Sorry, the related image is too large.";
                header("Location: donateindex.php?error=$em");
                exit();
            } else {
                $rel_img_ex_lc = strtolower(pathinfo($rel_img_name, PATHINFO_EXTENSION));
                if (in_array($rel_img_ex_lc, $allowed_exs)) {
                    $rel_img_name_sanitized = preg_replace("/[^a-zA-Z0-9.]/", "_", $rel_img_name);
                    $rel_img_upload_path = 'uploads/related/' . $rel_img_name_sanitized;
                    move_uploaded_file($rel_tmp_name, $rel_img_upload_path);

                    // Insert the sanitized file name into the database
                    $linkrelimg = $rel_img_name_sanitized;
                } else {
                    $em = "You can't upload files of this type for related image.";
                    header("Location: donateindex.php?error=$em");
                    exit();
                }
            }
        } else {
            $em = "Error uploading the related image.";
            header("Location: donateindex.php?error=$em");
            exit();
        }
    }

    // Insert all data into the donation_form table
    $sql = "INSERT INTO donation_form (first_name, last_name, age, sex, email, phone, organization, province, city, barangay, street, artifact_title, artifact_description, acquisition_details, additional_info, narrative, link_art_img, artifact_images, link_doc_img, documentation, link_rel_img, related_images)
            VALUES ('$firstName', '$lastName', '$age', '$sex', '$email', '$phone', '$organization', '$province', '$city', '$barangay', '$street', '$artifactTitle', '$artifactDescription', '$acquisition', '$additionalInfo', '$narrative', '$linkartimg', '$art_img_name_sanitized', '$linkdocimg', '$doc_img_name_sanitized', '$linkrelimg', '$rel_img_name_sanitized')";

    if ($conn->query($sql) === TRUE) {
        date_default_timezone_set('Asia/Manila'); // or use your desired timezone

        // Prepare and insert into donations table
        $donor_name = $firstName . ' ' . $lastName;  // Concatenate first and last name
        $item_name = $artifactTitle;  // Use artifact title directly
        $donation_date = (new DateTime())->format('Y-m-d'); // Get the current date explicitly
        $status = "TO REVIEW";
        $transfer_status = "Pending"; // Default value, can be changed later

        $sql_donations = "INSERT INTO donations (donation_date, donor_name, item_name, status, transfer_status)
                          VALUES ('$donation_date', '$donor_name', '$item_name', '$status', '$transfer_status')";

        if ($conn->query($sql_donations) === TRUE) {
            echo "Donation record inserted successfully!";
        } else {
            echo "Error: " . $conn->error;
        }

        header("Location: donateindex.php");  // Redirect to the thank you page after successful submission
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
