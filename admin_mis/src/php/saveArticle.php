<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and collect form data
    $title = mysqli_real_escape_string($connextion, $_POST['article_title']);
    $type = mysqli_real_escape_string($connextion, $_POST['article_type']);
    $location = mysqli_real_escape_string($connextion, $_POST['article_location']);
    $author = mysqli_real_escape_string($conn, $_POST['article_author']);
    $content_left = mysqli_real_escape_string($connextion, $_POST['content-left']);
    $content_right = mysqli_real_escape_string($connextion, $_POST['content-right']);
    $content_image2 = mysqli_real_escape_string($connextion, $_POST['content-image2']);
    $content_image3 = mysqli_real_escape_string($connextion, $_POST['content-image3']);
    $imageDetails = mysqli_real_escape_string($connextion, $_POST['image-details']); // Image details (text)

    // Handle image uploads
    $image1 = $_FILES['image-1'];
    $image2 = $_FILES['image-2'];
    $image3 = $_FILES['image-3'];

    // Process image uploads
    $image1Path = uploadImage($image1);
    $image2Path = uploadImage($image2);
    $image3Path = uploadImage($image3);

    if ($image1Path && $image2Path && $image3Path) {
        // Insert the article into the database
        $query = "INSERT INTO articles (article_title, article_type, location, author, imgu1, imgu1_details, 
                  p1box_left, p1box_right, imgu2, p2box, p3box, imgu3, created_at, updated_date) 
                  VALUES ('$title', '$type', '$location', '$author', '$image1Path', '$imageDetails', 
                          '$content_left', '$content_right', '$image2Path', '$content_image2', 
                          '$content_image3', '$image3Path', CURRENT_TIMESTAMP, NULL)";

        if (mysqli_query($connextion, $query)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($connextion)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Image upload failed']);
    }
}

// Function to handle image uploads
function uploadImage($image) {
    $targetDir = "admin_mis\src\uploads\articlesUploads";
    $targetFile = $targetDir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    if (getimagesize($image["tmp_name"]) === false) {
        return false;
    }

    // Check if file already exists
    if (file_exists($targetFile)) {
        return false;
    }

    // Check file size (3MB limit)
    if ($image["size"] > 3 * 1024 * 1024) {
        return false;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
        return false;
    }

    // Try to upload file
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        return $targetFile;
    } else {
        return false;
    }
}
?>
