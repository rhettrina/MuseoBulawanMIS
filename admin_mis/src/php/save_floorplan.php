<?php
// Include the DB connection details
require_once 'db_connect.php'; // Ensure this file contains the correct $connextion variable

// Check if the connection was successful
if (!$connextion) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Make sure that the required fields exist in the POST data
if (!isset($_POST['unique_id']) || !isset($_POST['name']) || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Unique ID, name, and image file are required.']);
    exit;
}

$unique_id = mysqli_real_escape_string($connextion, $_POST['unique_id']);
$name = mysqli_real_escape_string($connextion, $_POST['name']);
$image = $_FILES['image'];

// Handle image upload
$imagePath = uploadImage($image);

if ($imagePath) {
    // Prepare and execute the database insert query
    $query = $connextion->prepare("INSERT INTO floorplans 
        (unique_id, name, image_path, created_at) 
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)");

    $query->bind_param('sss', $unique_id, $name, $imagePath);

    if ($query->execute()) {
        echo json_encode(['success' => true, 'message' => 'Floorplan saved successfully.', 'path' => $imagePath]);
    } else {
        echo json_encode(['success' => false, 'error' => $query->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Image upload failed']);
}

// Function to handle image upload
function uploadImage($image) {
    // Define target directory to save images
    $targetDir = dirname(__DIR__, 1) . "/uploads/layout-made/";
    $targetFile = $targetDir . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an image
    if (getimagesize($image["tmp_name"]) === false) {
        return "File is not an image.";
    }

    // Check if file already exists, use the same path
    if (file_exists($targetFile)) {
        return $targetFile; // Return the existing file path
    }

    // Check file size (max 3MB)
    if ($image["size"] > 3 * 1024 * 1024) {
        return "File size exceeds 3MB.";
    }

    // Check file format (allowed types: jpg, jpeg, png, gif)
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        return "Only JPG, JPEG, PNG, and GIF files are allowed.";
    }

    // Try to move the uploaded file to the target directory
    if (move_uploaded_file($image["tmp_name"], $targetFile)) {
        return 'uploads/layout-made/' . basename($image["name"]); // Return the relative path
    } else {
        return "Error uploading the file: " . $image["error"];
    }
}
?>
