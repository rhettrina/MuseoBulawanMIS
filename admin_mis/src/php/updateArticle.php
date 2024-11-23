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
    $content_image2 = $_POST["imgu2"] ?? "";
    $content_image3 = $_POST["imgu3"] ?? "";

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
            imgu2 = ?, 
            imgu3 = ?, 
            updated_date = CURRENT_TIMESTAMP
        WHERE id = ?");
    
    $stmt->bind_param(
        "sssssssssi", 
        $article_author, 
        $article_title, 
        $article_location, 
        $article_type, 
        $image_details, 
        $content_left, 
        $content_right, 
        $content_image2, 
        $content_image3, 
        $id
    );

    if (!$stmt->execute()) {
        $response["error"] = "Failed to update article: " . $stmt->error;
        echo json_encode($response);
        exit;
    }

    // Handle image uploads (image_1, image_2, image_3)
    $imageFields = ["imgu1", "imgu2", "imgu3"];  // Match the field names with the form input names
    foreach ($imageFields as $imageField) {
        if (isset($_FILES['imgu1']) && $_FILES['imgu1']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imgu1']['tmp_name'];
            $fileName = time() . '_' . $_FILES['imgu1']['name'];
            $uploadDir = 'uploads/';
        
            // Debug: Check if file was received
            error_log("File received: " . $_FILES['imgu1']['name']);
            error_log("Temp path: " . $fileTmpPath);
            error_log("Destination: " . $uploadDir . $fileName);
        
            // Ensure the directory exists or create it
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        
            $destination = $uploadDir . $fileName;
            if (move_uploaded_file($fileTmpPath, $destination)) {
                echo "File uploaded successfully!";
            } else {
                echo "Error moving file.";
            }
        } else {
            error_log("File upload error: " . $_FILES['imgu1']['error']);
        }
        
        
         // If no new file was uploaded, skip updating the image field
    }

    // If everything was successful
    $response["success"] = true;
} else {
    $response["error"] = "Invalid request method.";
}

// Send response back as JSON
echo json_encode($response);
?>
