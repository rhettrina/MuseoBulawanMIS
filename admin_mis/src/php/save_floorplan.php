<?php
// Include the DB connection details
require_once 'db_connect.php'; // Ensure this file contains the correct $connextion variable

// Check if the connection was successful
if (!$connextion) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Retrieve raw POST data
$data = file_get_contents('php://input');
$json = json_decode($data, true);

// Make sure that the required keys exist in the POST data
if (!isset($json['unique_id']) || !isset($json['name']) || !isset($json['imageData']) || !isset($_FILES['image'])) {
    echo json_encode(['success' => false, 'error' => 'Unique ID, name, image data, and image file are required.']);
    exit;
}

// Sanitize the input data to prevent SQL injection
$unique_id = mysqli_real_escape_string($connextion, $json['unique_id']);
$name = mysqli_real_escape_string($connextion, $json['name']);
$imageData = $json['imageData'];
$image = $_FILES['image'];

// Debugging: Log the $_FILES array
// This will help ensure the image is being received
error_log(print_r($_FILES, true));

// Handle image upload
$imagePath = uploadImage($image);

// Check if image upload is successful
if ($imagePath && $imagePath !== "Error") {
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
    echo json_encode(['success' => false, 'error' => 'Image upload failed: ' . $imagePath]);
}

// Function to handle image upload
function uploadImage($image) {
    // Define the target directory to save images
    $targetDir = dirname(__DIR__, 1) . "/uploads/layout-made/";

    // Ensure the directory exists
    if (!is_dir($targetDir)) {
        return "Upload directory does not exist.";
    }

    // Generate the target file path
    $targetFile = $targetDir . basename($image["name"]);

    // Debugging: Log the target directory and file path
    error_log("Target directory: " . $targetDir);
    error_log("Target file path: " . $targetFile);

    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an image
    if (getimagesize($image["tmp_name"]) === false) {
        return "File is not an image.";
    }

    // Check if the file already exists
    if (file_exists($targetFile)) {
        return "File already exists: " . $targetFile;
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
