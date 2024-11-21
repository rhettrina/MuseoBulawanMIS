<?php
// Include the DB connection details
require_once 'db_connect.php'; // Ensure this file contains the correct $connextion variable

// Check if the connection was successful
if (!$connextion) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Sanitize and collect form data
$article_title = mysqli_real_escape_string($connextion, $_POST['article_title']);
$article_author = mysqli_real_escape_string($connextion, $_POST['article_author']);
$article_location = mysqli_real_escape_string($connextion, $_POST['article_location']);
$article_type = mysqli_real_escape_string($connextion, $_POST['article_type']);
$content_left = mysqli_real_escape_string($connextion, $_POST['content-left']);
$content_right = mysqli_real_escape_string($connextion, $_POST['content-right']);
$content_image2 = mysqli_real_escape_string($connextion, $_POST['content-image2']);
$content_image3 = mysqli_real_escape_string($connextion, $_POST['content-image3']);
$image_details = mysqli_real_escape_string($connextion, $_POST['image-details']); // Image details (text)

// Handle image uploads
$image1 = $_FILES['image-1'];
$image2 = $_FILES['image-2'];
$image3 = $_FILES['image-3'];

// Process image uploads
$image1Path = uploadImage($image1);
$image2Path = uploadImage($image2);
$image3Path = uploadImage($image3);

if ($image1Path && $image2Path && $image3Path) {
    // Prepare and execute the database insert query
    $query = $connextion->prepare("INSERT INTO articles 
        (article_title, article_type, location, author, imgu1, imgu1_details, 
        p1box_left, p1box_right, imgu2, p2box, p3box, imgu3, created_at, updated_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)");

    $query->bind_param('ssssssssssss', 
        $article_title, $article_type, $article_location, $article_author, $image1Path, $image_details, 
        $content_left, $content_right, $image2Path, $content_image2, 
        $content_image3, $image3Path);

    if ($query->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $query->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Image upload failed']);
}

// Function to handle image upload
function uploadImage($image) {
    $targetDir = __DIR__ . "/../uploads/articlesUploads/";
    $targetFile = $targetDir . basename($image["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $errorMessage = "";

    // Check if the file is an image
    if (getimagesize($image["tmp_name"]) === false) {
        $errorMessage = "File is not an image.";
        return $errorMessage;
    }

    // Check if the file already exists
    if (file_exists($targetFile)) {
        $errorMessage = "File already exists.";
        return $errorMessage;
    }

    // Check file size (max 3MB)
    if ($image["size"] > 3 * 1024 * 1024) {
        $errorMessage = "File size exceeds 3MB.";
        return $errorMessage;
    }

    // Check file format (allowed types: jpg, jpeg, png, gif)
    if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif") {
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
?>
