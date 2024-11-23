<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db_connect.php'; // Include the database connextion

$response = ["success" => false, "error" => ""]; // Default response

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve text inputs
        $id = $_POST["id"] ?? null;
        $article_title = $_POST["article_title"] ?? "";
        $article_type = $_POST["article_type"] ?? "";
        $location = $_POST["location"] ?? "";
        $author = $_POST["author"] ?? "";
        $imgu1_details = $_POST["imgu1_details"] ?? "";
        $content_left = $_POST["p1box_left"] ?? "";
        $content_right = $_POST["p1box_right"] ?? "";
        $p2box = $_POST["p2box"] ?? "";
        $p3box = $_POST["p3box"] ?? "";

        // Validate required fields
        if (empty($id) || empty($article_title) || empty($author)) {
            throw new Exception("Missing required fields.");
        }

        // Sanitize inputs to prevent SQL injection
        $article_title = $connextion->real_escape_string($article_title);
        $article_type = $connextion->real_escape_string($article_type);
        $location = $connextion->real_escape_string($location);
        $author = $connextion->real_escape_string($author);
        $imgu1_details = $connextion->real_escape_string($imgu1_details);
        $content_left = $connextion->real_escape_string($content_left);
        $content_right = $connextion->real_escape_string($content_right);
        $p2box = $connextion->real_escape_string($p2box);
        $p3box = $connextion->real_escape_string($p3box);
        $id = (int)$id;

        // Update article details (excluding images)
        $query = "UPDATE articles SET 
                    article_title = '$article_title', 
                    article_type = '$article_type', 
                    location = '$location', 
                    author = '$author', 
                    imgu1_details = '$imgu1_details', 
                    p1box_left = '$content_left', 
                    p1box_right = '$content_right', 
                    p2box = '$p2box', 
                    p3box = '$p3box', 
                    updated_date = CURRENT_TIMESTAMP
                  WHERE id = $id";

        if (!$connextion->query($query)) {
            throw new Exception("Failed to update article: " . $connextion->error);
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

                // Validate the uploaded file type (only images allowed)
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $fileType = mime_content_type($fileTmpPath);

                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Invalid file type for $imageField. Only JPEG, PNG, and GIF files are allowed.");
                }

                // Ensure the file is not too large (example: max 5MB)
                if ($_FILES[$imageField]['size'] > 5 * 1024 * 1024) {
                    throw new Exception("File for $imageField is too large. Max size is 5MB.");
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
            // Update the image fields in the database directly
            $imgu1 = $uploadedImages['imgu1'] ?? '';
            $imgu2 = $uploadedImages['imgu2'] ?? '';
            $imgu3 = $uploadedImages['imgu3'] ?? '';

            // Construct the update query for images
            $imageQuery = "UPDATE articles SET 
                            imgu1 = '$imgu1', 
                            imgu2 = '$imgu2', 
                            imgu3 = '$imgu3' 
                            WHERE id = $id";

            if (!$connextion->query($imageQuery)) {
                throw new Exception("Failed to update image fields: " . $connextion->error);
            }
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
