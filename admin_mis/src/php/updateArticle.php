<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require 'db_connect.php'; // Include the database connection

$response = ["success" => false, "error" => ""]; // Default response

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Retrieve text inputs
        $id = $_POST["id"] ?? null;
        $article_title = $_POST["article_title"] ?? "";
        $article_type = $_POST["article_type"] ?? "";
        $location = $_POST["location"] ?? "";
        $author = $_POST["article_author"] ?? "";
        $imgu1_details = $_POST["image_details"] ?? "";
        $content_left = $_POST["content_left"] ?? "";
        $content_right = $_POST["content_right"] ?? "";
        $p2box = $_POST["content_box2"] ?? "";
        $p3box = $_POST["content_box3"] ?? "";

        // Validate required fields
        if (empty($id) || empty($article_title) || empty($author)) {
            throw new Exception("Missing required fields.");
        }

        // Handle file uploads
        $targetDir = dirname(__DIR__, 1) . "/uploads/articlesUploads/"; // Directory path
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        // Ensure the directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
        }

        // Array to store uploaded images
        $uploadedImages = [];
        $imageFields = ['imgu1', 'imgu2', 'imgu3'];

        // Loop through each image field and handle the upload
        foreach ($imageFields as $imageField) {
            if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES[$imageField]['tmp_name'];
                $fileName = time() . '_' . $_FILES[$imageField]['name']; // Ensure unique file name
                $destination = $targetDir . $fileName; // Destination file path

                // Validate the file type and size
                $fileType = mime_content_type($fileTmpPath);
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Invalid file type for $imageField. Allowed types: JPEG, PNG, GIF.");
                }

                if ($_FILES[$imageField]['size'] > 5 * 1024 * 1024) {
                    throw new Exception("File size for $imageField exceeds 5MB limit.");
                }

                // Move the file to the target directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $uploadedImages[$imageField] = $fileName; // Store the file name
                } else {
                    throw new Exception("Error moving file for $imageField.");
                }
            }
        }

        // Retrieve uploaded file names or keep existing ones
        $imgu1 = $uploadedImages['imgu1'] ?? $_POST['existing_imgu1'] ?? '';
        $imgu2 = $uploadedImages['imgu2'] ?? $_POST['existing_imgu2'] ?? '';
        $imgu3 = $uploadedImages['imgu3'] ?? $_POST['existing_imgu3'] ?? '';

        // Escaping the input to prevent SQL injection
        $article_title = $connextion->real_escape_string($article_title);
        $article_type = $connextion->real_escape_string($article_type);
        $location = $connextion->real_escape_string($location);
        $author = $connextion->real_escape_string($author);
        $imgu1_details = $connextion->real_escape_string($imgu1_details);
        $content_left = $connextion->real_escape_string($content_left);
        $content_right = $connextion->real_escape_string($content_right);
        $p2box = $connextion->real_escape_string($p2box);
        $p3box = $connextion->real_escape_string($p3box);

        // Prepare the SQL query using direct variable substitution (escaped for security)
        $query = "UPDATE articles SET 
                    article_title = '$article_title', 
                    article_type = '$article_type', 
                    location = '$location', 
                    author = '$author', 
                    imgu1 = '$imgu1', 
                    imgu1_details = '$imgu1_details', 
                    p1box_left = '$content_left', 
                    p1box_right = '$content_right', 
                    imgu2 = '$imgu2', 
                    p2box = '$p2box', 
                    p3box = '$p3box', 
                    imgu3 = '$imgu3', 
                    updated_date = NOW() 
                  WHERE id = $id";

        // Execute the query
        if ($connextion->query($query)) {
            $response["success"] = true;
        } else {
            throw new Exception("Failed to update article.");
        }
    }
} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

echo json_encode($response);
?>
