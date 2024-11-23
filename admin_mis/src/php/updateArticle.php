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

        // Handle file uploads
        $uploadDir = 'uploads/articlesUploads/';
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

        $uploadedImages = [];
        $imageFields = ['imgu1', 'imgu2', 'imgu3'];

        foreach ($imageFields as $imageField) {
            if (isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES[$imageField]['tmp_name'];
                $fileName = time() . '_' . $_FILES[$imageField]['name'];
                $destination = $uploadDir . $fileName;

                // Ensure the directory exists
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Validate the file type and size
                $fileType = mime_content_type($fileTmpPath);
                if (!in_array($fileType, $allowedTypes)) {
                    throw new Exception("Invalid file type for $imageField. Allowed types: JPEG, PNG, GIF.");
                }

                if ($_FILES[$imageField]['size'] > 5 * 1024 * 1024) {
                    throw new Exception("File size for $imageField exceeds 5MB limit.");
                }

                // Move file to target directory
                if (move_uploaded_file($fileTmpPath, $destination)) {
                    $uploadedImages[$imageField] = $fileName;
                } else {
                    throw new Exception("Error moving file for $imageField.");
                }
            }
        }

        // Retrieve uploaded file names or keep existing ones
        $imgu1 = $uploadedImages['imgu1'] ?? $_POST['existing_imgu1'] ?? '';
        $imgu2 = $uploadedImages['imgu2'] ?? $_POST['existing_imgu2'] ?? '';
        $imgu3 = $uploadedImages['imgu3'] ?? $_POST['existing_imgu3'] ?? '';

        // Prepare and execute the update query
        $query = "UPDATE articles SET 
                    article_title = ?, 
                    article_type = ?, 
                    location = ?, 
                    author = ?, 
                    imgu1 = ?, 
                    imgu1_details = ?, 
                    p1box_left = ?, 
                    p1box_right = ?, 
                    imgu2 = ?, 
                    p2box = ?, 
                    p3box = ?, 
                    imgu3 = ?, 
                    updated_date = ? 
                  WHERE id = ?";

        $stmt = $connextion->prepare($query);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $connextion->error);
        }

        $updated_date = date("Y-m-d H:i:s");
        $stmt->bind_param(
            "sssssssssssssi",
            $article_title,
            $article_type,
            $location,
            $author,
            $imgu1,
            $imgu1_details,
            $content_left,
            $content_right,
            $imgu2,
            $p2box,
            $p3box,
            $imgu3,
            $updated_date,
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }

        $stmt->close();

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
