<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db_connect.php'; // Include the database connection

$response = ["success" => false, "error" => ""]; // Default response

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve text inputs
        $id = $_POST["id"] ?? null;
        $article_author = $_POST["article_author"] ?? "";
        $article_title = $_POST["article_title"] ?? "";
        $article_location = $_POST["article_location"] ?? "";
        $article_type = $_POST["article_type"] ?? "";
        $image_details = $_POST["image_details"] ?? "";
        $content_left = $_POST["content_left"] ?? "";
        $content_right = $_POST["content_right"] ?? "";

        // Validate required fields
        if (empty($id) || empty($article_author) || empty($article_title)) {
            throw new Exception("Missing required fields.");
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

        // Binding params
        $stmt->bind_param(
            "sssssssi", 
            $article_author, 
            $article_title, 
            $article_location, 
            $article_type, 
            $image_details, 
            $content_left, 
            $content_right, 
            $id
        );

        $stmt->execute();

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
                    throw new Exception("Error moving file for $imageField.");
                }
            }
        }

        // If any images were uploaded, update the database
        if (!empty($uploadedImages)) {
            $stmt = $connextion->prepare("UPDATE articles SET imgu1 = ?, imgu2 = ?, imgu3 = ? WHERE id = ?");

            $stmt->bind_param(
                "sssi", // 4 variables: 3 strings and 1 integer
                $uploadedImages['imgu1'] ?? '', 
                $uploadedImages['imgu2'] ?? '', 
                $uploadedImages['imgu3'] ?? '', 
                $id
            );

            $stmt->execute();
        }

        // Successful response
        $response["success"] = true;
    } else {
        throw new Exception("Invalid request method.");
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

// Send response back as JSON
echo json_encode($response);
?>