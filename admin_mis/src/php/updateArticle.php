<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require 'db_connect.php'; // Include the database connection

$response = ["success" => false, "error" => ""]; // Default response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve text inputs
    $id = $_POST["id"] ?? null;
    $article_author = $_POST["article_author"] ?? "";
    $article_title = $_POST["article_title"] ?? "";
    $article_location = $_POST["location"] ?? "";
    $article_type = $_POST["article_type"] ?? "";
    $image_details = $_POST["imgu1_details"] ?? "";
    $content_left = $_POST["p1box_left"] ?? "";
    $content_right = $_POST["p1box_right"] ?? "";

    // Validate required fields
    if (empty($id) || empty($article_author) || empty($article_title)) {
        $response["error"] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }

    // Update main fields (article details)
    $stmt = $connextion->prepare("UPDATE articles 
        SET 
            author = ?, 
            article_title = ?, 
            location = ?, 
            article_type = ?, 
            imgu1_details = ?, 
            p1box_left = ?, 
            p1box_right = ?, 
            updated_date = CURRENT_TIMESTAMP
        WHERE id = ?");
    
    // Binding params for the first query
    $stmt->bind_param(
        "ssssssssi", 
        $article_author, 
        $article_title, 
        $article_location, 
        $article_type, 
        $image_details, 
        $content_left, 
        $content_right, 
        $id
    );

    if (!$stmt->execute()) {
        $response["error"] = "Failed to update article: " . $stmt->error;
        echo json_encode($response);
        exit;
    }

    // Handle image uploads (imgu1, imgu2, imgu3)
    $imageFields = ["imgu1", "imgu2", "imgu3"];
    $uploadDir = 'uploads/articlesUploads/';
    $uploadedImages = [];  // Array to hold the uploaded image file names
    
    foreach ($imageFields as $imageField) {
        if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$imageField]['tmp_name'];
            $fileName = time() . '_' . $_FILES[$imageField]['name']; // Make filename unique
            $destination = $uploadDir . $fileName;
            
            // Ensure the directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Move the uploaded file to the target directory
            if (move_uploaded_file($fileTmpPath, $destination)) {
                $uploadedImages[$imageField] = $fileName;  // Store the file name
            } else {
                $response["error"] = "Error moving file for $imageField.";
                echo json_encode($response);
                exit;
            }
        }
    }

    // If any images were uploaded, update the database
    if (!empty($uploadedImages)) {
        $stmt = $connextion->prepare("UPDATE articles SET imgu1 = ?, imgu2 = ?, imgu3 = ? WHERE id = ?");
        
        // Binding the image fields and id correctly
        $stmt->bind_param(
            "sssi", // Correct type definition: three strings for images and one integer for id
            $uploadedImages['imgu1'] ?? '', 
            $uploadedImages['imgu2'] ?? '', 
            $uploadedImages['imgu3'] ?? '', 
            $id // id is an integer
        );

        if (!$stmt->execute()) {
            $response["error"] = "Failed to update image fields: " . $stmt->error;
            echo json_encode($response);
            exit;
        }
    }

    // Successful response
    $response["success"] = true;
} else {
    $response["error"] = "Invalid request method.";
}

// Send response back as JSON
echo json_encode($response);
?>
