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
    $image_details = $_POST["image-details"] ?? "";
    $content_left = $_POST["content-left"] ?? "";
    $content_right = $_POST["content-right"] ?? "";
    $content_image2 = $_POST["content-image2"] ?? "";
    $content_image3 = $_POST["content-image3"] ?? "";

    // Validate required fields
    if (empty($id) || empty($article_author) || empty($article_title)) {
        $response["error"] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }

    // Update main fields (article details)
    $stmt = $connextion->prepare("
        UPDATE articles 
        SET 
            author = ?, 
            article_title = ?, 
            location = ?, 
            article_type = ?, 
            image_details = ?, 
            content_left = ?, 
            content_right = ?, 
            content_image2 = ?, 
            content_image3 = ? 
        WHERE id = ?
    ");
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
    $imageFields = ["image_1", "image_2", "image_3"];
    foreach ($imageFields as $imageField) {
        if (isset($_FILES[$imageField]) && $_FILES[$imageField]["error"] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$imageField]["tmp_name"];
            $fileName = time() . "_" . basename($_FILES[$imageField]["name"]);
            $uploadDir = "uploads/";

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $destination = $uploadDir . $fileName;

            // Move the file to the uploads folder
            if (move_uploaded_file($fileTmpPath, $destination)) {
                // Update the database with the new image path for the respective field
                $stmt = $connextion->prepare("UPDATE articles SET $imageField = ? WHERE id = ?");
                $stmt->bind_param("si", $destination, $id);

                if (!$stmt->execute()) {
                    $response["error"] = "Failed to update $imageField: " . $stmt->error;
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response["error"] = "Failed to upload $imageField.";
                echo json_encode($response);
                exit;
            }
        } // If no new file was uploaded, skip updating the image field
    }

    // If everything was successful
    $response["success"] = true;
} else {
    $response["error"] = "Invalid request method.";
}

// Send response back as JSON
echo json_encode($response);
?>
