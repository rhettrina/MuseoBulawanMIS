<?php
require 'db_connect.php'; // Include the database connection

$response = ["success" => false, "error" => ""]; // Default response

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve text inputs
    $id = $_POST["id"] ?? null;
    $article_title = $_POST["article_title"] ?? "";
    $article_type = $_POST["article_type"] ?? "";
    $location = $_POST["article_location"] ?? "";
    $author = $_POST["article_author"] ?? "";
    $imgu1_details = $_POST["image-details"] ?? "";
    $p1box_left = $_POST["content-left"] ?? "";
    $p1box_right = $_POST["content-right"] ?? "";
    $p2box = $_POST["content-image2"] ?? ""; // Textarea input
    $p3box = $_POST["content-image3"] ?? ""; // Textarea input

    // Validate required fields
    if (empty($id) || empty($article_title) || empty($author)) {
        $response["error"] = "Missing required fields.";
        echo json_encode($response);
        exit;
    }

    // Handle created_at and updated_date timestamps
    $updated_date = date("Y-m-d H:i:s");

    // Initialize placeholders for images
    $imgu1 = $imgu2 = $imgu3 = null;

    // Handle image uploads
    $imageFields = [
        "update-image-1-input" => "imgu1",
        "update-image-2-input" => "imgu2",
        "update-image-3-input" => "imgu3"
    ];

    foreach ($imageFields as $inputName => $dbColumn) {
        if (isset($_FILES[$inputName]) && $_FILES[$inputName]["error"] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES[$inputName]["tmp_name"];
            $fileName = time() . "_" . basename($_FILES[$inputName]["name"]);
            $uploadDir = "uploads/";

            // Ensure upload directory exists
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    $response["error"] = "Failed to create upload directory.";
                    echo json_encode($response);
                    exit;
                }
            }

            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                // Assign the uploaded file path to the corresponding variable
                $$dbColumn = $destination;
            } else {
                $response["error"] = "Failed to upload file for $inputName.";
                echo json_encode($response);
                exit;
            }
        }
    }

    // Prepare the update query
    $stmt = $connextion->prepare("
        UPDATE articles 
        SET 
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
        WHERE id = ?
    ");

    $stmt->bind_param(
        "ssssssssssssi", 
        $article_title, 
        $article_type, 
        $location, 
        $author, 
        $imgu1, 
        $imgu1_details, 
        $p1box_left, 
        $p1box_right, 
        $imgu2, 
        $p2box, 
        $p3box, 
        $imgu3, 
        $updated_date, 
        $id
    );

    if (!$stmt->execute()) {
        $response["error"] = "Failed to update article: " . $stmt->error;
        echo json_encode($response);
        exit;
    }

    // If everything was successful
    $response["success"] = true;
} else {
    $response["error"] = "Invalid request method.";
}

// Send response back as JSON
echo json_encode($response);
?>
