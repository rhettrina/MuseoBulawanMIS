<?php
// save_floorplan.php

header('Content-Type: application/json');

// Include the DB connection details
require_once 'db_connect.php'; // Ensure this file contains the correct $connection variable

// Check if the connection was successful
if (!$connection) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// Retrieve raw POST data
$data = file_get_contents('php://input');
$json = json_decode($data, true);

if (!isset($json['unique_id']) || !isset($json['imageData'])) {
    echo json_encode(['success' => false, 'error' => 'Unique ID and image data are required.']);
    exit;
}

$unique_id = mysqli_real_escape_string($connection, $json['unique_id']);
$imageData = $json['imageData'];

// Validate and decode the base64 image
if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
    $imageType = strtolower($type[1]); // jpg, png, gif, etc.
    
    // Check allowed image types
    if (!in_array($imageType, ['jpg', 'jpeg', 'png', 'gif'])) {
        echo json_encode(['success' => false, 'error' => 'Unsupported image type']);
        exit;
    }

    $imageData = substr($imageData, strpos($imageData, ',') + 1);
    $imageData = base64_decode($imageData);

    if ($imageData === false) {
        echo json_encode(['success' => false, 'error' => 'Base64 decode failed']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid image data']);
    exit;
}

// Define the directory and filename
$directory = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'layout-made' . DIRECTORY_SEPARATOR;

if (!file_exists($directory)) {
    if (!mkdir($directory, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'Failed to create directories']);
        exit;
    }
}

$filename = "floorplan_" . $unique_id . "." . ($imageType === 'jpeg' ? 'jpg' : $imageType);
$filePath = $directory . $filename;

// Check if the file already exists
if (file_exists($filePath)) {
    echo json_encode(['success' => false, 'error' => 'File already exists']);
    exit;
}

// Save the image to the directory
if (file_put_contents($filePath, $imageData)) {
    // Prepare the image path for storage (relative path)
    $relativePath = 'layout-made/' . $filename;

    // Insert into the database
    $stmt = $connection->prepare("INSERT INTO floorplans (unique_id, image_path) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $unique_id, $relativePath);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Image saved successfully.', 'path' => $relativePath]);
        } else {
            // Remove the uploaded file if database insertion fails
            unlink($filePath);
            echo json_encode(['success' => false, 'error' => 'Database insertion failed: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        // Remove the uploaded file if statement preparation fails
        unlink($filePath);
        echo json_encode(['success' => false, 'error' => 'Database statement preparation failed: ' . $connection->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save the image file.']);
}
?>