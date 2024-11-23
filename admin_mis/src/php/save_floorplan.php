<?php
// save_floorplan.php

// Disable error display
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log'); // Log errors to a file in the same directory

header('Content-Type: application/json');

header('Content-Type: application/json');

// Include the DB connection details
require_once 'db_connect.php'; // Ensure this file contains the correct $connection variable

try {
    // Check if the connection was successful
    if (!$connection) {
        throw new Exception('Database connection failed');
    }

    // Retrieve raw POST data
    $data = file_get_contents('php://input');
    $json = json_decode($data, true);

    if (!isset($json['unique_id']) || !isset($json['imageData'])) {
        throw new Exception('Unique ID and image data are required.');
    }

    $unique_id = mysqli_real_escape_string($connection, $json['unique_id']);
    $imageData = $json['imageData'];

    // Validate and decode the base64 image
    if (!preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
        throw new Exception('Invalid image data');
    }

    $imageType = strtolower($type[1]); // jpg, png, gif, etc.
    if (!in_array($imageType, ['jpg', 'jpeg', 'png', 'gif'])) {
        throw new Exception('Unsupported image type');
    }

    $imageData = substr($imageData, strpos($imageData, ',') + 1);
    $imageData = base64_decode($imageData);
    if ($imageData === false) {
        throw new Exception('Base64 decode failed');
    }

    // Define the directory and filename
    $directory = __DIR__ . DIRECTORY_SEPARATOR . 'layout-made' . DIRECTORY_SEPARATOR;

    if (!file_exists($directory) && !mkdir($directory, 0755, true)) {
        throw new Exception('Failed to create directories');
    }

    $filename = "floorplan_" . $unique_id . "." . ($imageType === 'jpeg' ? 'jpg' : $imageType);
    $filePath = $directory . $filename;

    // Check if the file already exists
    if (file_exists($filePath)) {
        throw new Exception('File already exists');
    }

    // Save the image to the directory
    if (!file_put_contents($filePath, $imageData)) {
        throw new Exception('Failed to save the image file.');
    }

    // Prepare the image path for storage (relative path)
    $relativePath = 'layout-made/' . $filename;

    // Insert into the database
    $stmt = $connection->prepare("INSERT INTO floorplans (unique_id, image_path) VALUES (?, ?)");
    if (!$stmt) {
        unlink($filePath); // Remove the uploaded file if statement preparation fails
        throw new Exception('Database statement preparation failed: ' . $connection->error);
    }

    $stmt->bind_param("ss", $unique_id, $relativePath);
    if (!$stmt->execute()) {
        unlink($filePath); // Remove the uploaded file if database insertion fails
        throw new Exception('Database insertion failed: ' . $stmt->error);
    }

    echo json_encode(['success' => true, 'message' => 'Image saved successfully.', 'path' => $relativePath]);

    $stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
