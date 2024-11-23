<?php
// save_floorplan.php

header('Content-Type: application/json');

// Include the DB connextion details
require_once 'db_connect.php'; // Ensure this file contains the correct $connextion variable

// Check if the connextion was successful
if (!$connextion) {
    echo json_encode(['success' => false, 'error' => 'Database connextion failed']);
    exit;
}

// Retrieve raw POST data
$data = file_get_contents('php://input');
$json = json_decode($data, true);

if (!isset($json['unique_id']) || !isset($json['name']) || !isset($json['imageData'])) {
    echo json_encode(['success' => false, 'error' => 'Unique ID, name, and image data are required.']);
    exit;
}

$unique_id = mysqli_real_escape_string($connextion, $json['unique_id']);
$name = mysqli_real_escape_string($connextion, $json['name']);
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

// Check if the name already exists in the database and increment it if necessary
$original_name = $name;
$counter = 1;

while (true) {
    $stmt = $connextion->prepare("SELECT COUNT(*) FROM floorplans WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        break; // No conflict, use this name
    }

    // If the name exists, append a number to the name and increment the counter
    $name = $original_name . "_" . $counter;
    $counter++;
}

// Define the directory and filename
$directory = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'admin_mis' . DIRECTORY_SEPARATOR . 'layout-editor' . DIRECTORY_SEPARATOR . 'layout-made' . DIRECTORY_SEPARATOR;

// Ensure the directory exists, create it if it doesn't
if (!file_exists($directory)) {
    if (!mkdir($directory, 0755, true)) {
        echo json_encode(['success' => false, 'error' => 'Failed to create directories']);
        exit;
    }
}

$filename = "floorplan_" . $unique_id . "_" . preg_replace('/[^a-zA-Z0-9]/', '_', $name) . "." . ($imageType === 'jpeg' ? 'jpg' : $imageType);
$filePath = $directory . $filename;

// Check if the file already exists
if (file_exists($filePath)) {
    echo json_encode(['success' => false, 'error' => 'File already exists']);
    exit;
}

// Save the image to the directory
if (file_put_contents($filePath, $imageData)) {
    // Prepare the image path for storage (relative path)
    $relativePath = 'layout-editor/layout-made/' . $filename;

    // Insert into the database
    $stmt = $connextion->prepare("INSERT INTO floorplans (unique_id, name, image_path, created_at) VALUES (?, ?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("sss", $unique_id, $name, $relativePath);

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
        echo json_encode(['success' => false, 'error' => 'Database statement preparation failed: ' . $connextion->error]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save the image file.']);
}
?>
